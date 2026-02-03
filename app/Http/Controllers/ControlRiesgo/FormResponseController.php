<?php

namespace App\Http\Controllers\ControlRiesgo;

use App\Http\Controllers\Controller;
use App\Models\FormVersion;
use App\Models\FormResponse;
use App\Models\FieldResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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

        $response->load(['formVersion.phases.fields', 'fieldResponses']);
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
