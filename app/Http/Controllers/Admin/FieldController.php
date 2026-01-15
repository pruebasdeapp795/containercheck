<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Fase;
use App\Models\Field;
use Illuminate\Support\Str; // Added this line

class FieldController extends Controller
{
    protected $schemaManager;

    public function __construct(\App\Services\SchemaManager $schemaManager)
    {
        $this->schemaManager = $schemaManager;
    }

    /**
     * Display a listing of the resource (Fields for a specific Phase).
     */
    public function index(Fase $phase)
    {
        $fields = $phase->fields()->orderBy('order')->get();
        return view('admin.fields.index', compact('phase', 'fields'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Fase $phase)
    {
        return view('admin.fields.create', compact('phase'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Fase $phase)
    {
        $validated = $request->validate([
            'label' => 'required|string|max:255',
            'type' => 'required|in:text,number,select,date,signature,photo',
            'required' => 'boolean',
            'options' => 'nullable|string', // JSON string or comma separated? Let's assume JSON string for now, or handle comma separated in logic
            'description' => 'nullable|string',
            'order' => 'integer',
        ]);

        // Checkbox handling
        $validated['required'] = $request->has('required');
        $validated['phase_id'] = $phase->id;
        $validated['column_name'] = Str::slug($validated['label'], '_');

        // Ensure column name is unique for this phase (simple check)
        $count = $phase->fields()->where('column_name', $validated['column_name'])->count();
        if ($count > 0) {
            $validated['column_name'] .= '_' . ($count + 1);
        }

        // Basic JSON handling for options if select
        if ($validated['type'] === 'select' && $request->filled('options')) {
            // If user entered comma separated values, convert to JSON
            $options = array_map('trim', explode(',', $request->input('options')));
            $validated['options'] = json_encode($options);
        }

        Field::create($validated);
        $this->schemaManager->addFieldColumn($phase->table_name, $validated['column_name'], $validated['type']);

        return redirect()->route('admin.phases.fields.index', $phase->id)->with('success', 'Campo creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Not used
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Field $field)
    {
        return view('admin.fields.edit', compact('field'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Field $field)
    {
        $validated = $request->validate([
            'label' => 'required|string|max:255',
            'type' => 'required|in:text,number,select,date,signature,photo',
            'required' => 'boolean',
            'options' => 'nullable|string',
            'description' => 'nullable|string',
            'order' => 'integer',
        ]);

        $validated['required'] = $request->has('required');

        if ($validated['type'] === 'select' && $request->filled('options')) {
            // If looks like JSON, keep it, else explode
            $inputOptions = $request->input('options');
            if (is_string($inputOptions) && !str_starts_with(trim($inputOptions), '[')) {
                $options = array_map('trim', explode(',', $inputOptions));
                $validated['options'] = json_encode($options);
            } else {
                $validated['options'] = $inputOptions;
            }
        }

        $field->update($validated);

        return redirect()->route('admin.phases.fields.index', $field->phase_id)->with('success', 'Campo actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Field $field)
    {
        $phaseId = $field->phase_id;
        $phase = $field->phase;

        $this->schemaManager->dropFieldColumn($phase->table_name, $field->column_name);
        $field->delete();

        return redirect()->route('admin.phases.fields.index', $phaseId)->with('success', 'Campo eliminado exitosamente.');
    }
}
