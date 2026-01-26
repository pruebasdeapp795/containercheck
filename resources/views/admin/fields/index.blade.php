@extends('layouts.app')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Campos: {{ $phase->name }}</h2>
            <p class="text-gray-600">{{ $phase->description }}</p>
        </div>

        <div>
            <a href="{{ route('admin.phases.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">
                <i class="fas fa-arrow-left"></i> Volver a Fases
            </a>
            <a href="{{ route('admin.phases.fields.create', $phase->id) }}"
                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                <i class="fas fa-plus"></i> Nuevo Campo
            </a>
        </div>
    </div>

    <div class="bg-white shadow-md rounded my-6 overflow-x-auto">
        <table class="min-w-full bg-white">
            <thead class="bg-gray-800 text-white">
                <tr>
                    <th class="w-1/12 text-left py-3 px-4 uppercase font-semibold text-sm">Validar</th>
                    <th class="w-1/3 text-left py-3 px-4 uppercase font-semibold text-sm">Etiqueta</th>
                    <th class="w-1/6 text-left py-3 px-4 uppercase font-semibold text-sm">Tipo</th>
                    <th class="w-1/6 text-left py-3 px-4 uppercase font-semibold text-sm">Requerido</th>
                    <th class="w-1/4 text-left py-3 px-4 uppercase font-semibold text-sm">Acciones</th>
                </tr>
            </thead>
            <tbody class="text-gray-700" id="fields-table-body">
                @forelse($fields as $field)
                    <tr class="border-b hover:bg-gray-100 cursor-move" data-id="{{ $field->id }}">
                        <td class="text-left py-3 px-4">{{ $field->order }}</td>
                        <td class="text-left py-3 px-4 font-bold">{{ $field->label }}</td>
                        <td class="text-left py-3 px-4">
                            <span class="bg-gray-200 rounded px-2 py-1 text-xs">{{ $field->type }}</span>
                        </td>
                        <td class="text-left py-3 px-4">
                            @if($field->required)
                                <span class="text-red-500 font-bold">Sí</span>
                            @else
                                <span class="text-gray-500">Opcional</span>
                            @endif
                        </td>
                        <td class="text-left py-3 px-4 flex gap-2">
                            <a href="{{ route('admin.fields.edit', $field->id) }}" class="text-blue-500 hover:text-blue-700"><i
                                    class="fas fa-edit"></i></a>

                            <form action="{{ route('admin.fields.destroy', $field->id) }}" method="POST"
                                onsubmit="return confirm('¿Eliminar este campo?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700"><i
                                        class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-4 text-center text-gray-500">No hay campos configurados para esta fase.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var el = document.getElementById('fields-table-body');
            Sortable.create(el, {
                animation: 150,
                onEnd: function () {
                    var ids = [];
                    el.querySelectorAll('tr[data-id]').forEach(function (row) {
                        ids.push(row.getAttribute('data-id'));
                    });

                    if (ids.length > 0) {
                        fetch('{{ route('admin.fields.reorder') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ ids: ids })
                        })
                            .then(response => response.json())
                            .then(data => {
                                console.log('Orden actualizado');
                            });
                    }
                }
            });
        });
    </script>
@endsection