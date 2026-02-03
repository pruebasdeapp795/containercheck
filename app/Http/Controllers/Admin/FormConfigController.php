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
            'type' => 'required|in:text,numeric,date,photo,select',
            'options' => 'nullable|string',
            'rejection_value' => 'nullable|string'
        ]);

        $phase->fields()->create([
            'label' => $request->label,
            'type' => $request->type,
            'options' => $request->options,
            'rejection_value' => $request->rejection_value,
            'order' => $phase->fields()->count()
        ]);

        return back()->with('success', 'Campo añadido.');
    }

    public function updateField(Request $request, Field $field)
    {
        $request->validate([
            'label' => 'required|string',
            'type' => 'required|in:text,numeric,date,photo,select',
            'options' => 'nullable|string',
            'rejection_value' => 'nullable|string'
        ]);

        $field->update([
            'label' => $request->label,
            'type' => $request->type,
            'options' => $request->options,
            'rejection_value' => $request->rejection_value,
            'is_visible' => $request->has('is_visible')
        ]);

        return back()->with('success', 'Campo actualizado.');
    }
}
