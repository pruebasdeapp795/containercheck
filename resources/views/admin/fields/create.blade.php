@extends('layouts.app')

@section('content')
    <div class="max-w-2xl mx-auto bg-white p-6 rounded shadow">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Agregar Campo a: {{ $phase->name }}</h2>
            <a href="{{ route('admin.phases.fields.index', $phase->id) }}" class="text-gray-600 hover:text-gray-900">
                <i class="fas fa-arrow-left"></i> Cancelar
            </a>
        </div>

        <form action="{{ route('admin.phases.fields.store', $phase->id) }}" method="POST">
            @csrf

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="label">
                    Etiqueta (Pregunta)
                </label>
                <input
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    id="label" name="label" type="text" required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="type">
                    Tipo de Campo
                </label>
                <select
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    id="type" name="type" required onchange="toggleOptions(this.value)">
                    <option value="text">Texto Corto</option>
                    <option value="number">Numérico</option>
                    <option value="select">Selección (Menú Desplegable)</option>
                    <option value="date">Fecha</option>
                    <option value="photo">Foto</option>
                    <option value="signature">Firma</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="description">
                    Descripción / Instrucción
                </label>
                <textarea
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    id="description" name="description"
                    placeholder="Instrucciones para el inspector (ej: Tomar foto frontal)"></textarea>
            </div>

            <div class="mb-4 hidden" id="options-container">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="options">
                    Opciones (Separadas por comas)
                </label>
                <input
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    id="options" name="options" type="text" placeholder="Opción 1, Opción 2, Opción 3">
            </div>

            <div class="mb-4 grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="order">
                        Orden
                    </label>
                    <input
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        id="order" name="order" type="number" value="1">
                </div>

                <div class="flex items-center mt-6">
                    <input type="checkbox" id="required" name="required" class="form-checkbox h-5 w-5 text-blue-600"
                        checked>
                    <label for="required" class="ml-2 text-gray-700">Requerido</label>
                </div>
            </div>

            <div class="flex items-center justify-end">
                <button
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                    type="submit">
                    Guardar Campo
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