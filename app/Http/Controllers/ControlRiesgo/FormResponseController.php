<?php

namespace App\Http\Controllers\ControlRiesgo;

use App\Http\Controllers\Controller;
use App\Models\FormVersion;
use App\Models\FormResponse;
use App\Models\FieldResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\InspectionSignature;
use Illuminate\Support\Facades\Hash;

class FormResponseController extends Controller
{
    public function index()
    {
        return redirect()->route('control-riesgo.index');
    }

    public function dashboard()
    {
        $user = Auth::user();
        $totalInspections = FormResponse::where('user_id', $user->id)->where('status', 'completed')->count();
        $recentInspections = FormResponse::where('user_id', $user->id)
            ->where('status', 'completed')
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

        return view('control-riesgo.dashboard', compact('totalInspections', 'recentInspections', 'openInspections', 'availableVersions'));
    }

    public function create(FormVersion $version)
    {
        $response = FormResponse::create([
            'user_id' => Auth::id(),
            'form_version_id' => $version->id,
            'status' => 'draft',
            'last_phase_completed' => -1
        ]);

        return redirect()->route('control-riesgo.inspecciones.edit', $response->id);
    }

    public function edit(FormResponse $response)
    {
        if ($response->user_id !== Auth::id() || $response->status !== 'draft') {
            abort(403);
        }

        $response->load(['formVersion.phases.fields', 'fieldResponses', 'inspectionSignatures.user']);
        $activeVersion = $response->formVersion;

        return view('control-riesgo.inspecciones.index', compact('response', 'activeVersion'));
    }

    public function savePhase(Request $request, FormResponse $response)
    {
        if ($response->user_id !== Auth::id())
            abort(403);

        $request->validate([
            'fields' => 'required|array',
            'phase_order' => 'required|integer'
        ]);

        foreach ($request->fields as $fieldId => $value) {
            if ($request->hasFile("fields.$fieldId")) {
                $path = $request->file("fields.$fieldId")->store('photos', 'public');
                $value = $path;
            }

            FieldResponse::updateOrCreate(
                ['form_response_id' => $response->id, 'field_id' => $fieldId],
                ['value' => $value]
            );
        }

        // Special Logic: Personal Del Cargue (Phase ID 13 Check via Field 65 - Cédula)
        // IDs: 57 (Nombre), 58 (Cargo), 59 (Chaleco), 65 (Cédula)
        if (isset($request->fields[65]) && !empty($request->fields[65])) {
            $cedula = $request->fields[65];
            $nombre = $request->fields[57] ?? 'Personal';
            $cargo = $request->fields[58] ?? 'Carga';

            // Find or Create User
            $personalUser = User::where('cedula', $cedula)->first();
            if (!$personalUser) {
                // Check if email exists to avoid error (though unlikely with this pattern)
                $email = $cedula . '@containercheck.local';
                if (!User::where('email', $email)->exists()) {
                    $personalUser = User::create([
                        'name' => $nombre,
                        'email' => $email,
                        'cedula' => $cedula,
                        'password' => Hash::make('12345678'), // Default Password
                        'role' => 'personal'
                    ]);
                } else {
                    $personalUser = User::where('email', $email)->first();
                }
            }

            $chaleco = $request->fields[59] ?? '';

            // Create Signature Requirement if not exists
            if ($personalUser) {
                InspectionSignature::firstOrCreate(
                    [
                        'form_response_id' => $response->id,
                        'user_id' => $personalUser->id
                    ],
                    [
                        'role_in_inspection' => $cargo,
                        'vest_number' => $chaleco,
                        'signed_at' => null // Pending signature
                    ]
                );
            }
        }

        // Check for ANY pending signatures before allowing progress
        $hasPendingSignatures = InspectionSignature::where('form_response_id', $response->id)
            ->whereNull('signed_at')
            ->exists();

        if ($hasPendingSignatures) {
            return response()->json([
                'success' => true,
                'pending_signatures' => true,
                'message' => 'Es necesaria la firma del personal para continuar.'
            ]);
        }

        if ($request->phase_order > $response->last_phase_completed) {
            $response->update(['last_phase_completed' => $request->phase_order]);
        }

        if ($request->has('is_rejected') && $request->is_rejected == '1') {
            $response->update(['status' => 'rejected', 'signed_at' => now()]);
        }

        return response()->json(['success' => true]);
    }

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
            'status' => 'completed'
        ]);

        return redirect()->route('control-riesgo.reportes')->with('success', 'Inspección realizada con éxito.');
    }

    public function history()
    {
        $user = Auth::user();
        $responses = FormResponse::where('user_id', $user->id)
            ->where('status', 'completed')
            ->with('formVersion')
            ->orderBy('created_at', 'desc')
            ->get();
        return view('control-riesgo.reportes.index', compact('responses'));
    }

    public function show(FormResponse $response)
    {
        if ($response->user_id !== Auth::id()) {
            abort(403);
        }

        $response->load(['formVersion.phases.fields', 'fieldResponses.field']);
        return view('control-riesgo.reportes.show', compact('response'));
    }
}
