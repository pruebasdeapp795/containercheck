@extends('layouts.app')

@section('content')
    <div class="max-w-2xl mx-auto bg-white p-6 rounded shadow">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Crear Nueva Fase</h2>
            <a href="{{ route('admin.phases.index') }}" class="text-gray-600 hover:text-gray-900">
                <i class="fas fa-arrow-left"></i> Cancelar
            </a>
        </div>

        <form action="{{ route('admin.phases.store') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="name">
                    Nombre de la Fase
                </label>
                <input
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    id="name" name="name" type="text" placeholder="Ej: Información del Vehículo" required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="description">
                    Descripción
                </label>
                <textarea
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    id="description" name="description"
                    placeholder="Breve descripción de lo que se inspecciona en esta fase"></textarea>
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
                    <input type="checkbox" id="is_active" name="is_active" value="1"
                        class="form-checkbox h-5 w-5 text-blue-600" checked>
                    <label for="is_active" class="ml-2 text-gray-700">Fase Activa</label>
                </div>
            </div>

            <div class="flex items-center justify-end">
                <button
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                    type="submit">
                    Guardar Fase
                </button>
            </div>
        </form>
    </div>
@endsection