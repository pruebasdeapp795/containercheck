@extends('layouts.app')

@section('content')
    <div class="max-w-2xl mx-auto bg-white p-6 rounded shadow">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Editar Campo</h2>
            <a href="{{ route('admin.phases.fields.index', $field->phase_id) }}" class="text-gray-600 hover:text-gray-900">
                <i class="fas fa-arrow-left"></i> Cancelar
            </a>
        </div>

        <form action="{{ route('admin.fields.update', $field->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="label">
                    Etiqueta (Pregunta)
                </label>
                <input
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    id="label" name="label" type="text" value="{{ $field->label }}" required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="type">
                    Tipo de Campo
                </label>
                <select
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    id="type" name="type" required onchange="toggleOptions(this.value)">
                    <option value="text" {{ $field->type == 'text' ? 'selected' : '' }}>Texto Corto</option>
                    <option value="number" {{ $field->type == 'number' ? 'selected' : '' }}>Numérico</option>
                    <option value="select" {{ $field->type == 'select' ? 'selected' : '' }}>Selección (Menú Desplegable)
                    </option>
                    <option value="date" {{ $field->type == 'date' ? 'selected' : '' }}>Fecha</option>
                    <option value="photo" {{ $field->type == 'photo' ? 'selected' : '' }}>Foto</option>
                    <option value="signature" {{ $field->type == 'signature' ? 'selected' : '' }}>Firma</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="description">
                    Descripción / Instrucción
                </label>
                <textarea
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    id="description" name="description">{{ $field->description }}</textarea>
            </div>

            @php
                $optionsStr = '';
                if ($field->options) {
                    $opts = json_decode($field->options);
                    if (is_array($opts)) {
                        $optionsStr = implode(', ', $opts);
                    }
                }
            @endphp

            <div class="mb-4 {{ $field->type == 'select' ? '' : 'hidden' }}" id="options-container">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="options">
                    Opciones (Separadas por comas)
                </label>
                <input
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    id="options" name="options" type="text" value="{{ $optionsStr }}"
                    placeholder="Opción 1, Opción 2, Opción 3">
            </div>

            <div class="mb-4 grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="order">
                        Orden
                    </label>
                    <input
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        id="order" name="order" type="number" value="{{ $field->order }}">
                </div>

                <div class="flex items-center mt-6">
                    <input type="checkbox" id="required" name="required" class="form-checkbox h-5 w-5 text-blue-600" {{ $field->required ? 'checked' : '' }}>
                    <label for="required" class="ml-2 text-gray-700">Requerido</label>
                </div>
            </div>

            <div class="flex items-center justify-end">
                <button
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                    type="submit">
                    Actualizar Campo
                </button>
            </div>
        </form>
    </div>

    <script>
        function toggleOptions(type) {
            const container = document.getElementById('options-container');
            if (type === 'select') {
                container.classList.remove('hidden');
            } else {
                container.classList.add('hidden');
            }
        }
    </script>
@endsection