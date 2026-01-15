<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Fase;

class PhaseController extends Controller
{
    protected $schemaManager;

    public function __construct(\App\Services\SchemaManager $schemaManager)
    {
        $this->schemaManager = $schemaManager;
    }

    public function index()
    {
        $phases = Fase::orderBy('order')->get();
        return view('admin.phases.index', compact('phases'));
    }

    public function create()
    {
        return view('admin.phases.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'order' => 'integer',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        // Auto-generate table name from name
        $validated['table_name'] = 'phase_' . \Illuminate\Support\Str::slug($validated['name'], '_');
        $validated['is_active'] = $request->has('is_active');

        $phase = Fase::create($validated);
        $this->schemaManager->createPhaseTable($phase->table_name);

        return redirect()->route('admin.phases.index')->with('success', 'Fase creada exitosamente.');
    }

    public function edit(string $id)
    {
        $phase = Fase::findOrFail($id);
        return view('admin.phases.edit', compact('phase'));
    }

    public function update(Request $request, string $id)
    {
        $phase = Fase::findOrFail($id);
        // Note: Renaming phases/tables logic omitted for simplicity or out of scope. 
        // We will just update metadata. If name changes, we usually DON'T rename table to avoid data loss issues or complexity.

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'order' => 'integer',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $phase->update($validated);

        return redirect()->route('admin.phases.index')->with('success', 'Fase actualizada exitosamente.');
    }

    public function destroy(string $id)
    {
        $phase = Fase::findOrFail($id);
        $this->schemaManager->dropPhaseTable($phase->table_name);
        $phase->delete();

        return redirect()->route('admin.phases.index')->with('success', 'Fase eliminada exitosamente.');
    }
}
