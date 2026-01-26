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

            <div class="mb-4">
                <div class="flex items-center">
                    <input type="checkbox" id="required" name="required" value="1"
                        class="form-checkbox h-5 w-5 text-blue-600" {{ $field->required ? 'checked' : '' }}>
                    <label for="required" class="ml-2 text-gray-700">Requerido</label>
                </div>
            </div>

            <div class="mb-4">
                <h3 class="text-lg font-semibold text-gray-700 mb-2 border-b pb-1">Lógica Condicional (Opcional)</h3>
                <p class="text-sm text-gray-500 mb-2">Este campo solo se mostrará si se cumple la condición.</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="conditional_linked_to" class="block text-gray-700 font-bold mb-2">Campo Padre
                            (Condición)</label>
                        <select name="conditionals[linked_to]" id="conditional_linked_to"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            <option value="">-- Ninguno (Siempre visible) --</option>
                            @foreach($fields as $parentField)
                                <option value="{{ $parentField->id }}" {{ (isset($field->conditionals['linked_to']) && $field->conditionals['linked_to'] == $parentField->id) ? 'selected' : '' }}>
                                    {{ $parentField->label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="conditional_value" class="block text-gray-700 font-bold mb-2">Mostrar si valor es igual
                            a:</label>
                        <input type="text" name="conditionals[value]" id="conditional_value"
                            value="{{ $field->conditionals['value'] ?? '' }}"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                            placeholder="Ej: SI">
                        <p class="text-xs text-gray-500 mt-1">Para checkbox usar '1' o 'on'. Para select, el valor exacto.
                        </p>
                    </div>
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