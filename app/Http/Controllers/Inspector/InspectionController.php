<?php

namespace App\Http\Controllers\Inspector;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Inspeccion;
use App\Models\Fase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class InspectionController extends Controller
{
    public function index()
    {
        $inspections = Inspeccion::with(['inspector', 'participant'])
            ->where('inspector_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('inspector.dashboard', compact('inspections'));
    }

    public function store(Request $request)
    {
        $inspection = Inspeccion::create([
            'inspector_id' => auth()->id(),
            'status' => 'in_progress', // Start directly in progress
        ]);

        return redirect()->route('inspector.inspections.edit', $inspection->id);
    }

    public function show(string $id)
    {
        // Maybe show a summary or redirect to edit if not completed
        return redirect()->route('inspector.inspections.edit', $id);
    }

    public function edit(string $id)
    {
        $inspection = Inspeccion::with(['signatures'])->where('inspector_id', auth()->id())->findOrFail($id);

        // Load phases and fields
        $phases = Fase::with([
            'fields' => function ($q) {
                $q->orderBy('order');
            }
        ])->orderBy('order')->get();

        // Load values from dynamic tables
        $values = collect([]);

        foreach ($phases as $phase) {
            if (Schema::hasTable($phase->table_name)) {
                $row = DB::table($phase->table_name)->where('inspection_id', $inspection->id)->first();

                if ($row) {
                    foreach ($phase->fields as $field) {
                        $col = $field->column_name;
                        if (property_exists($row, $col)) {
                            // Mocking the structure expected by the view or passing a simpler array
                            // View Expects: $inspection->values->where('field_id', $field->id)->first()->value
                            // Let's attach 'values' to inspection object manually or change view.
                            // Easier to create a collection of objects with field_id and value properties.
                            $values->push((object) [
                                'field_id' => $field->id,
                                'value' => $row->$col
                            ]);
                        }
                    }
                }
            }
        }

        // Temporarily attach to inspection for the view to work without major changes
        $inspection->setRelation('values', $values);

        $canEdit = in_array($inspection->status, ['draft', 'in_progress']);

        // Check for specific permission if needed (e.g. if user is admin overriding)
        // For now, simple status check + ownership (already in findOrFail)

        return view('inspector.inspections.edit', compact('inspection', 'phases', 'canEdit'));
    }

    public function update(Request $request, string $id)
    {
        $inspection = Inspeccion::where('inspector_id', auth()->id())->findOrFail($id);

        $validated = $request->validate([
            'values' => 'array', // key=field_id, value=value
            'status' => 'sometimes|in:draft,in_progress,pending_signature,completed',
            'signature' => 'nullable|string', // Base64 signature
        ]);

        if ($request->has('values')) {
            // Group values by Phase to minimize DB queries
            $fields = \App\Models\Field::with('phase')->findMany(array_keys($validated['values']));
            $dataByPhase = [];

            foreach ($fields as $field) {
                // Handle File Upload
                if ($request->hasFile("values.{$field->id}")) {
                    $file = $request->file("values.{$field->id}");
                    $path = $file->store("inspections/{$inspection->id}", 'public');
                    $value = $path;
                } elseif (isset($validated['values'][$field->id])) {
                    $value = $validated['values'][$field->id];
                } else {
                    continue;
                }

                if ($value === null)
                    continue;

                $phaseId = $field->phase_id;
                if (!isset($dataByPhase[$phaseId])) {
                    $dataByPhase[$phaseId] = [
                        'table' => $field->phase->table_name,
                        'data' => []
                    ];
                }
                $dataByPhase[$phaseId]['data'][$field->column_name] = $value;

                // Participant logic (Legacy support hook)
                if ($field->phase && $field->phase->order === 4) {
                    if (str_contains(strtolower($field->label), 'documento')) {
                        $documentNumber = $value;
                        if ($documentNumber) {
                            $participant = User::firstOrCreate(
                                ['document_number' => $documentNumber],
                                [
                                    'name' => 'Participante ' . $documentNumber,
                                    'email' => null,
                                    'password' => Hash::make($documentNumber),
                                    'role' => 'participant',
                                ]
                            );
                            $inspection->participant_id = $participant->id;
                            $inspection->save();
                        }
                    }
                    if (str_contains(strtolower($field->label), 'nombre') && $inspection->participant_id) {
                        $participant = User::find($inspection->participant_id);
                        if ($participant && $value) {
                            $participant->name = $value;
                            $participant->save();
                        }
                    }
                }
            }

            // Perform DB updates per phase table
            foreach ($dataByPhase as $phaseId => $info) {
                $tableName = $info['table'];
                $data = $info['data'];
                if (empty($data))
                    continue;

                // Ensure inspection_id is set
                // Use updateOrInsert
                \Illuminate\Support\Facades\DB::table($tableName)->updateOrInsert(
                    ['inspection_id' => $inspection->id],
                    array_merge($data, ['updated_at' => now()])
                );
            }
        }

        if ($request->has('status')) {
            $inspection->status = $request->input('status');
            $inspection->save();
        }

        // Handle Auto-Signature if provided (e.g. from hidden input)
        // Check SignatureController logic essentially here if needed, 
        // but let's keep it simple: form saves values. Signature button is separate or part of form?
        // Let's assume signature is handled via separate AJAX or a final step. 

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Guardado correctamente.']);
        }

        return redirect()->route('inspector.inspections.edit', $inspection->id)
            ->with('success', 'Inspección guardada.');
    }

    public function destroy(string $id)
    {
        $inspection = Inspeccion::where('inspector_id', auth()->id())->findOrFail($id);
        $inspection->delete();
        return redirect()->route('inspector.inspections.index')->with('success', 'Inspección eliminada.');
    }
}
