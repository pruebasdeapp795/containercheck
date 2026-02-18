<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FormVersion;
use App\Models\Phase;
use App\Models\Field;
use Illuminate\Http\Request;

class FormConfigController extends Controller
{
    public function index()
    {
        $versions = FormVersion::orderBy('created_at', 'desc')->get();
        return view('admin.forms.index', compact('versions'));
    }

    public function storeVersion(Request $request)
    {
        $request->validate([
            'version' => 'required|string|unique:form_versions,version'
        ]);

        $version = FormVersion::create([
            'version' => $request->version,
            'is_active' => false
        ]);

        return redirect()->route('admin.forms.show', $version->id)->with('success', 'Versión creada con éxito.');
    }

    public function show(FormVersion $version)
    {
        $version->load('phases.fields');
        return view('admin.forms.show', compact('version'));
    }

    public function activate(FormVersion $version)
    {
        FormVersion::where('id', '!=', $version->id)->update(['is_active' => false]);
        $version->update(['is_active' => true]);

        return back()->with('success', 'Versión activada.');
    }

    public function duplicate(FormVersion $version)
    {
        // Crear una nueva versión basada en la versión original
        $newVersion = FormVersion::create([
            'version' => $version->version . ' - Copia',
            'is_active' => false
        ]);

        // Copiar todas las fases
        foreach ($version->phases as $phase) {
            $newPhase = $newVersion->phases()->create([
                'name' => $phase->name,
                'order' => $phase->order,
                'is_visible' => $phase->is_visible ?? true
            ]);

            // Copiar todos los campos de cada fase
            foreach ($phase->fields as $field) {
                $newPhase->fields()->create([
                    'label' => $field->label,
                    'type' => $field->type,
                    'options' => $field->options,
                    'rejection_value' => $field->rejection_value,
                    'is_required' => $field->is_required ?? true,
                    'is_precinto' => $field->is_precinto ?? false,
                    'order' => $field->order,
                    'is_visible' => $field->is_visible ?? true
                ]);
            }
        }

        return redirect()->route('admin.forms.show', $newVersion->id)->with('success', 'Versión duplicada con éxito. Ahora puedes modificarla.');
    }

    public function storePhase(Request $request, FormVersion $version)
    {
        $request->validate(['name' => 'required|string']);

        $version->phases()->create([
            'name' => $request->name,
            'order' => $version->phases()->count()
        ]);

        return back()->with('success', 'Fase añadida.');
    }

    public function updatePhase(Request $request, Phase $phase)
    {
        $request->validate(['name' => 'required|string']);

        $phase->update([
            'name' => $request->name,
            'is_visible' => $request->has('is_visible')
        ]);

        return back()->with('success', 'Fase actualizada.');
    }

    public function storeField(Request $request, Phase $phase)
    {
        $request->validate([
            'label' => 'required|string',
            'type' => 'required|in:text,numeric,date,time,photo,select',
            'options' => 'nullable|string',
            'rejection_value' => 'nullable|string',
            'is_required' => 'nullable|boolean',
            'is_precinto' => 'nullable|boolean'
        ]);

        $phase->fields()->create([
            'label' => $request->label,
            'type' => $request->type,
            'options' => $request->options,
            'rejection_value' => $request->rejection_value,
            'is_required' => $request->has('is_required'),
            'is_precinto' => $request->has('is_precinto'),
            'order' => $phase->fields()->count()
        ]);

        return back()->with('success', 'Campo añadido.');
    }

    public function updateField(Request $request, Field $field)
    {
        $request->validate([
            'label' => 'required|string',
            'type' => 'required|in:text,numeric,date,time,photo,select',
            'options' => 'nullable|string',
            'rejection_value' => 'nullable|string',
            'is_required' => 'nullable|boolean',
            'is_precinto' => 'nullable|boolean'
        ]);

        $field->update([
            'label' => $request->label,
            'type' => $request->type,
            'options' => $request->options,
            'rejection_value' => $request->rejection_value,
            'is_required' => $request->has('is_required'),
            'is_precinto' => $request->has('is_precinto'),
            'is_visible' => $request->has('is_visible')
        ]);

        return back()->with('success', 'Campo actualizado.');
    }

    public function reorderPhases(Request $request)
    {
        $request->validate(['order' => 'required|array']);

        foreach ($request->order as $index => $id) {
            Phase::where('id', $id)->update(['order' => $index]);
        }

        return response()->json(['success' => true]);
    }

    public function reorderFields(Request $request)
    {
        $request->validate(['order' => 'required|array']);

        foreach ($request->order as $index => $id) {
            Field::where('id', $id)->update(['order' => $index]);
        }

        return response()->json(['success' => true]);
    }

    public function deletePhase(Phase $phase)
    {
        // Verificar que la versión no esté activa
        if ($phase->formVersion->is_active) {
            return back()->with('error', 'No puedes eliminar fases de una versión activa.');
        }

        $phase->delete();
        return back()->with('success', 'Fase eliminada con éxito.');
    }

    public function deleteField(Field $field)
    {
        // Verificar que la versión no esté activa
        if ($field->phase->formVersion->is_active) {
            return back()->with('error', 'No puedes eliminar campos de una versión activa.');
        }

        $field->delete();
        return back()->with('success', 'Campo eliminado con éxito.');
    }
}
