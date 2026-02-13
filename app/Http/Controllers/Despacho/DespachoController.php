<?php

namespace App\Http\Controllers\Despacho;

use App\Http\Controllers\ControlRiesgo\FormResponseController;
use App\Models\FieldResponse;
use App\Models\FormResponse;
use App\Models\FormVersion;
use App\Models\Precinto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Field;

class DespachoController extends FormResponseController
{
    public function index()
    {
        $user = Auth::user();
        $totalInspections = FormResponse::where('user_id', $user->id)
            ->whereIn('status', ['completed', 'pending_monitoreo'])
            ->count();

        $recentInspections = FormResponse::where('user_id', $user->id)
            ->whereIn('status', ['completed', 'pending_monitoreo'])
            ->with('formVersion')
            ->latest()
            ->take(5)
            ->get();

        $openInspections = FormResponse::where('user_id', $user->id)
            ->where('status', 'draft')
            ->with(['formVersion', 'formVersion.phases'])
            ->latest()
            ->get();

        $availableVersions = FormVersion::where('is_active', true)->get();

        // This view will be specifically for Despacho
        return view('despacho.dashboard', compact('totalInspections', 'recentInspections', 'openInspections', 'availableVersions'));
    }

    // Override create to redirect to Despacho edit route
    public function create(FormVersion $version)
    {
        $response = FormResponse::create([
            'user_id' => Auth::id(),
            'form_version_id' => $version->id,
            'status' => 'draft',
            'last_phase_completed' => -1
        ]);

        return redirect()->route('despacho.inspecciones.edit', $response->id);
    }

    // Override edit to return Despacho view if needed (or reuse ControlRiesgo view if it's role-agnostic)
    public function edit(FormResponse $response)
    {
        if ($response->user_id !== Auth::id() || $response->status !== 'draft') {
            abort(403);
        }

        $response->load(['formVersion.phases.fields', 'fieldResponses', 'inspectionSignatures.user']);
        $activeVersion = $response->formVersion;

        // Reusing the control-riesgo view because form logic is complex and shared
        // But need to ensure routes inside the view are dynamic or point correctly.
        // The form views post to 'inspecciones.savePhase' and 'store'.
        // If I name my routes 'despacho.inspecciones.savePhase' and 'despacho.store', I might need to update the view to use dynamic route names.
        // Or I can use the existing controller for savePhase if I route correctly.

        // Let's check the view `control-riesgo.inspecciones.index`.
        // If it uses hardcoded routes, I might need to duplicate/adapt it.

        return view('despacho.inspecciones.index', compact('response', 'activeVersion'));
    }

    // Override store to redirect to Despacho dashboard
    public function store(Request $request, FormResponse $response)
    {
        if ($response->user_id !== Auth::id())
            abort(403);

        $request->validate([
            'signature' => 'required|string',
        ]);

        $response->update([
            'signature' => $request->signature,
            'signed_at' => now(),
            'status' => 'pending_monitoreo'
        ]);

        // Automatically calculate and save "Hora de Terminación Inspección" if the field exists
        $endTimeField = Field::where('label', 'Hora de Terminación Inspección')
            ->whereHas('phase', function ($query) use ($response) {
                $query->where('form_version_id', $response->form_version_id);
            })->first();

        if ($endTimeField) {
            FieldResponse::updateOrCreate(
                ['form_response_id' => $response->id, 'field_id' => $endTimeField->id],
                ['value' => now()->format('H:i')]
            );
        }

        return redirect()->route('despacho.index')->with('success', 'Inspección firmada. Pendiente aprobación de Monitoreo.');
    }

    // Inventory Methods (Copied from MonitoreoController but adapted for Despacho view)
    public function inventario()
    {
        $precintos = Precinto::orderBy('created_at', 'desc')->get();
        return view('despacho.inventario.index', compact('precintos'));
    }

    public function storePrecinto(Request $request)
    {
        $request->validate([
            'codigo' => 'required|string',
            'tipo' => 'required|string',
            'cantidad' => 'required|integer|min:1',
        ]);

        Precinto::create([
            'codigo' => $request->codigo,
            'tipo' => $request->tipo,
            'cantidad' => $request->cantidad,
            'fecha_ingreso' => now(),
            'estado' => 'disponible',
        ]);

        return redirect()->back()->with('success', 'Precinto agregado correctamente.');
    }
}
