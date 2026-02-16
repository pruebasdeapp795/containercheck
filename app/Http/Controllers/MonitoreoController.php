<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\FormResponse;
use App\Models\Precinto;
use Illuminate\Support\Facades\Auth;

class MonitoreoController extends Controller
{
    public function index()
    {
        // Pending inspections (status = pending_monitoreo)
        $pending = FormResponse::where('status', 'pending_monitoreo')
            ->with(['formVersion', 'user'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Completed by this monitoreo user or all completed? 
        // Showing all completed inspections for reference
        $completed = FormResponse::where('status', 'completed')
            ->with(['formVersion', 'user', 'monitoreoUser'])
            ->orderBy('created_at', 'desc')
            ->take(50)
            ->get();

        return view('monitoreo.index', compact('pending', 'completed'));
    }

    public function show(FormResponse $response)
    {
        // Ensure the response is in a state that monitoreo can see
        if ($response->status === 'draft') {
            abort(403, 'Inspección aún en borrador.');
        }

        $response->load(['formVersion.phases.fields', 'fieldResponses', 'inspectionSignatures.user', 'user']);

        return view('monitoreo.show', compact('response'));
    }

    public function liberate(Request $request, FormResponse $response)
    {
        if ($response->status !== 'pending_monitoreo') {
            abort(403, 'Estado inválido para liberación.');
        }

        $user = Auth::user();

        // Check if user has a saved signature
        if (!$user->saved_signature) {
            return redirect()->back()->with('error', 'Debe cargar su firma antes de liberar inspecciones.');
        }

        $response->update([
            'monitoreo_signature' => $user->saved_signature,
            'monitoreo_signed_at' => now(),
            'monitoreo_user_id' => Auth::id(),
            'status' => 'completed'
        ]);

        return redirect()->route('monitoreo.index')->with('success', 'Inspección liberada correctamente.');
    }

    public function uploadSignature(Request $request)
    {
        $request->validate([
            'signature_image' => 'required|image|mimes:png,jpg,jpeg|max:2048',
        ]);

        $user = Auth::user();

        // Delete old signature if exists
        if ($user->saved_signature && file_exists(public_path($user->saved_signature))) {
            unlink(public_path($user->saved_signature));
        }

        // Store new signature
        $file = $request->file('signature_image');
        $filename = 'signatures/' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('signatures'), $filename);

        $user->update([
            'saved_signature' => '/' . $filename
        ]);

        return redirect()->back()->with('success', 'Firma cargada correctamente. Ahora puede liberar inspecciones.');
    }

    // Visualizador Methods
    public function indexViewer()
    {
        $responses = FormResponse::whereIn('status', ['pending_monitoreo', 'completed'])
            ->with(['formVersion', 'user'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('visualizador.index', compact('responses'));
    }

    public function showViewer(FormResponse $response)
    {
        if ($response->status === 'draft') {
            abort(403, 'Inspección aún en borrador.');
        }

        $response->load(['formVersion.phases.fields', 'fieldResponses', 'inspectionSignatures.user', 'user', 'monitoreoUser']);
        return view('visualizador.show', compact('response'));
    }


    public function inventario()
    {
        $precintos = Precinto::where('estado', '!=', 'usado')->orderBy('created_at', 'desc')->get();

        // Group by type and state 'disponible' to show what can be transferred
        $disponiblesPorTipo = Precinto::where('estado', 'disponible')
            ->select('tipo', \Illuminate\Support\Facades\DB::raw('count(*) as total'))
            ->groupBy('tipo')
            ->get();

        return view('visualizador.inventario', compact('precintos', 'disponiblesPorTipo'));
    }

    public function storePrecinto(Request $request)
    {
        $request->validate([
            'codigos' => 'required|string',
            'tipo' => 'required|string',
        ]);

        $codigos = preg_split('/\r\n|\r|\n/', $request->codigos);
        $codigos = array_filter(array_map('trim', $codigos));

        if (empty($codigos)) {
            return redirect()->back()->with('error', 'Debe ingresar al menos un código.');
        }

        $count = 0;
        foreach ($codigos as $codigo) {
            // Check if code already exists to avoid duplicates
            if (!Precinto::where('codigo', $codigo)->exists()) {
                Precinto::create([
                    'codigo' => $codigo,
                    'tipo' => $request->tipo,
                    'cantidad' => 1,
                    'fecha_ingreso' => now(),
                    'estado' => 'disponible',
                ]);
                $count++;
            }
        }

        if ($count === 0) {
            return redirect()->back()->with('error', 'Todos los códigos ya existen en el inventario.');
        }

        return redirect()->back()->with('success', "$count precintos agregados correctamente.");
    }

    public function trasladarALogistica(Request $request)
    {
        $request->validate([
            'tipo' => 'required|string',
            'cantidad' => 'required|integer|min:1',
        ]);

        // Find available seals of that type
        $disponibles = Precinto::where('tipo', $request->tipo)
            ->where('estado', 'disponible')
            ->take($request->cantidad)
            ->get();

        if ($disponibles->count() < $request->cantidad) {
            return redirect()->back()->with('error', "No hay suficientes precintos de tipo {$request->tipo} disponibles. Disponibles: " . $disponibles->count());
        }

        // Crear registro en Logistica (Acta de Traslado)
        $logistica = \App\Models\Logistica::create([
            'tipo' => $request->tipo,
            'cantidad' => $request->cantidad,
            'fecha_traslado' => now(),
            'user_id' => Auth::id(),
        ]);

        // Actualizar los precintos seleccionados
        Precinto::whereIn('id', $disponibles->pluck('id'))->update([
            'estado' => 'en_logistica',
            'logistica_id' => $logistica->id
        ]);

        return redirect()->back()->with('success', "{$request->cantidad} precintos de tipo {$request->tipo} trasladados a logística correctamente.");
    }

    public function searchPrecinto(Request $request)
    {
        $query = $request->get('q');
        $tipo = $request->get('tipo');

        $precintos = Precinto::where('estado', 'en_logistica')
            ->where('codigo', 'like', "%{$query}%")
            ->when($tipo, function ($q) use ($tipo) {
                // If special mapping is needed, handle it here
                if ($tipo === 'Botella') {
                    return $q->where('tipo', 'Botella');
                }
                if ($tipo === 'Guaya') {
                    return $q->where('tipo', 'Guaya');
                }
                if ($tipo === 'Satelital') {
                    return $q->where('tipo', 'Satelital');
                }
                return $q->where('tipo', $tipo);
            })
            ->limit(10)
            ->get(['codigo', 'tipo']);

        return response()->json($precintos);
    }
}
