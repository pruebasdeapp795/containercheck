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
use App\Models\Field;
use Illuminate\Support\Facades\Mail;
use App\Mail\InspectionRejected;

class FormResponseController extends Controller
{
    public function index()
    {
        return redirect()->route('control-riesgo.index');
    }

    public function dashboard()
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

            // Handle arrays (multipersonal) by encoding to JSON
            $dbValue = is_array($value) ? json_encode($value) : $value;

            FieldResponse::updateOrCreate(
                ['form_response_id' => $response->id, 'field_id' => $fieldId],
                ['value' => $dbValue]
            );

            // Logic to consume seals (Precintos)
            // Fields: 164 (Precinto 01), 165 (Precinto 02), 166 (Satelital)
            if (in_array($fieldId, [164, 165, 166])) {
                // If there was a previous seal for this field in this response, free it
                $oldValue = FieldResponse::where('form_response_id', $response->id)
                    ->where('field_id', $fieldId)
                    ->first()?->value;

                if ($oldValue && $oldValue !== $value) {
                    \App\Models\Precinto::where('codigo', $oldValue)
                        ->where('form_response_id', $response->id)
                        ->update([
                            'estado' => 'en_logistica',
                            'form_response_id' => null,
                            'usado_at' => null
                        ]);
                }

                // Mark new seal as used
                if (!empty($value)) {
                    // Try to find container number
                    $containerNumber = $request->fields[60] ?? $request->fields[69] ?? $request->fields[77] ?? null;

                    if (!$containerNumber) {
                        $containerNumber = FieldResponse::where('form_response_id', $response->id)
                            ->whereIn('field_id', [60, 69, 77])
                            ->first()?->value;
                    }

                    \App\Models\Precinto::where('codigo', $value)
                        ->update([
                            'estado' => 'usado',
                            'form_response_id' => $response->id,
                            'numero_contenedor' => $containerNumber,
                            'usado_at' => now()
                        ]);
                }
            }
        }

        // Special Logic: Personal Del Cargue (Phase ID 13 Check via Field 65 - Cédula)
        // IDs: 57 (Nombre), 58 (Cargo), 59 (Chaleco), 65 (Cédula)
        if (isset($request->fields[65])) {
            $cedulas = $request->fields[65];
            // Normalize to array
            if (!is_array($cedulas))
                $cedulas = [$cedulas];

            $nombres = $request->fields[57] ?? [];
            if (!is_array($nombres))
                $nombres = [$nombres];

            $cargos = $request->fields[58] ?? [];
            if (!is_array($cargos))
                $cargos = [$cargos];

            $chalecos = $request->fields[59] ?? [];
            if (!is_array($chalecos))
                $chalecos = [$chalecos];

            foreach ($cedulas as $index => $cedula) {
                if (empty($cedula))
                    continue;

                $nombre = $nombres[$index] ?? 'Personal';
                $cargo = $cargos[$index] ?? 'Carga';
                $chaleco = $chalecos[$index] ?? '';

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

            // Enviar correo de notificación por rechazo
            $emails = config('mail.rejection_emails');
            if (!empty($emails)) {
                $recipientList = array_map('trim', explode(',', $emails));
                Mail::to($recipientList)->send(new InspectionRejected($response));
            }
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

        return redirect()->route('control-riesgo.reportes')->with('success', 'Inspección firmada. Pendiente aprobación de Monitoreo.');
    }

    public function history()
    {
        $user = Auth::user();
        $responses = FormResponse::where('user_id', $user->id)
            ->whereIn('status', ['completed', 'pending_monitoreo'])
            ->with('formVersion')
            ->orderBy('created_at', 'desc')
            ->get();
        return view('control-riesgo.reportes.index', compact('responses'));
    }

    public function searchUserByCedula($cedula)
    {
        $user = User::where('cedula', $cedula)->first();

        if ($user) {
            return response()->json([
                'found' => true,
                'name' => $user->name,
                'cedula' => $user->cedula
            ]);
        }

        return response()->json([
            'found' => false
        ]);
    }

    public function searchUsers(Request $request)
    {
        $query = $request->get('q');
        if (empty($query))
            return response()->json([]);

        $users = User::where('cedula', 'LIKE', "$query%")
            ->orWhere('name', 'LIKE', "%$query%")
            ->take(10)
            ->get(['id', 'name', 'cedula']);

        return response()->json($users);
    }

    public function show(FormResponse $response)
    {
        if ($response->user_id !== Auth::id()) {
            abort(403);
        }

        if ($response->status === 'draft') {
            return redirect()->route('control-riesgo.inspecciones.edit', $response->id);
        }

        $response->load(['formVersion.phases.fields', 'fieldResponses.field']);
        return view('control-riesgo.reportes.show', compact('response'));
    }

    public function gallery(FormResponse $response)
    {
        if (!in_array($response->status, ['completed', 'pending_monitoreo']) && $response->user_id !== Auth::id()) {
            abort(403);
        }

        $response->load(['formVersion.phases.fields', 'fieldResponses.field']);
        $photos = [];

        foreach ($response->fieldResponses as $fr) {
            if ($fr->field->type === 'photo' && $fr->value) {
                $decoded = json_decode($fr->value, true);
                if (is_array($decoded)) {
                    foreach ($decoded as $index => $path) {
                        if ($path) {
                            $photos[] = [
                                'path' => $path,
                                'label' => $fr->field->label . " (#" . ($index + 1) . ")"
                            ];
                        }
                    }
                } else {
                    $photos[] = [
                        'path' => $fr->value,
                        'label' => $fr->field->label
                    ];
                }
            }
        }

        return view('control-riesgo.reportes.gallery', compact('response', 'photos'));
    }
}
