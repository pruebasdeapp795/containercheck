@extends('layouts.app')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Gestión de Fases</h2>

        <div>
            <a href="{{ route('admin.users.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">
                <i class="fas fa-arrow-left"></i> Volver a Usuarios
            </a>
            <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
                onclick="window.location='{{ route('admin.phases.create') }}'">
                <i class="fa-solid fa-user-plus"></i> Nueva Fase
            </button>
        </div>
    </div>

    <div class="bg-white shadow-md rounded my-6 overflow-x-auto">
        <table class="min-w-full bg-white">
            <thead class="text-white" style="background-color: #002c73">
                <tr>
                    <th class="w-1/12 text-left py-3 px-4 uppercase font-semibold text-sm">Orden</th>
                    <th class="w-1/4 text-left py-3 px-4 uppercase font-semibold text-sm">Nombre</th>
                    <th class="w-1/3 text-left py-3 px-4 uppercase font-semibold text-sm">Descripción</th>
                    <th class="w-1/6 text-left py-3 px-4 uppercase font-semibold text-sm">Estado</th>
                    <th class="w-1/6 text-left py-3 px-4 uppercase font-semibold text-sm">Acciones</th>
                </tr>
            </thead>
            <tbody class="text-gray-700" id="phases-table-body">
                @foreach($phases as $phase)
                    <tr class="border-b hover:bg-gray-100 cursor-move" data-id="{{ $phase->id }}">
                        <td class="text-left py-3 px-4">{{ $phase->order }}</td>
                        <td class="text-left py-3 px-4 font-bold">{{ $phase->name }}</td>
                        <td class="text-left py-3 px-4 text-sm">{{ $phase->description }}</td>
                        <td class="text-left py-3 px-4">
                            <span class="bg-green-200 rounded-full px-3 py-1 text-sm font-semibold text-green-700">
                                {{ $phase->is_active ? 'Activa' : 'Inactiva' }}
                            </span>
                        </td>
                        <td class="text-left py-3 px-4 flex gap-2">
                            <button class="text-blue-500 hover:text-blue-700" title="Editar Fase"
                                onclick="window.location='{{ route('admin.phases.edit', $phase->id) }}'"><i
                                    class="fas fa-edit"></i></button>
                            <a href="{{ route('admin.phases.fields.index', $phase->id) }}"
                                class="text-indigo-500 hover:text-indigo-700" title="Gestionar Campos"><i
                                    class="fas fa-list-ul"></i> Campos</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var el = document.getElementById('phases-table-body');
            Sortable.create(el, {
                animation: 150,
                onEnd: function () {
                    var ids = [];
                    el.querySelectorAll('tr').forEach(function (row) {
                        ids.push(row.getAttribute('data-id'));
                    });

                    fetch('{{ route('admin.phases.reorder') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ ids: ids })
                    })
                        .then(response => response.json())
                        .then(data => {
                            // Optional: update UI or show notification
                            console.log('Orden actualizado');
                        });
                }
            });
        });
    </script>
@endsection