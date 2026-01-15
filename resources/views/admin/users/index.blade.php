@extends('layouts.app')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Gestión de Usuarios (Administrador)</h2>
        <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            <i class="fas fa-plus"></i> Nuevo Usuario
        </button>
    </div>

    <div class="bg-white shadow-md rounded my-6 overflow-x-auto">
        <table class="min-w-full bg-white grid-cols-1">
            <thead class="bg-gray-800 text-white">
                <tr>
                    <th class="w-1/4 text-left py-3 px-4 uppercase font-semibold text-sm">Nombre</th>
                    <th class="w-1/4 text-left py-3 px-4 uppercase font-semibold text-sm">Email / Doc</th>
                    <th class="w-1/4 text-left py-3 px-4 uppercase font-semibold text-sm">Rol</th>
                    <th class="w-1/4 text-left py-3 px-4 uppercase font-semibold text-sm">Acciones</th>
                </tr>
            </thead>
            <tbody class="text-gray-700">
                @foreach($users as $user)
                    <tr class="border-b hover:bg-gray-100">
                        <td class="w-1/4 text-left py-3 px-4">{{ $user->name }}</td>
                        <td class="w-1/4 text-left py-3 px-4">{{ $user->email ?? $user->document_number }}</td>
                        <td class="w-1/4 text-left py-3 px-4">
                            <span class="bg-gray-200 rounded-full px-3 py-1 text-sm font-semibold text-gray-700 mr-2">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td class="w-1/4 text-left py-3 px-4 flex gap-2">
                            <button class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></button>
                            <button class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="p-4">
            {{ $users->links() }}
        </div>
    </div>

    <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Quick Links / Functions -->
        <div class="bg-white p-6 rounded shadow">
            <h3 class="font-bold text-xl mb-4">Accesos Rápidos</h3>
            <ul class="list-disc pl-5 space-y-2 text-blue-600">
                <li><a href="{{ route('admin.phases.index') }}" class="hover:underline">Gestionar Fases de Inspección</a>
                </li>
                <li><a href="#" class="hover:underline">Configurar Campos Personalizados</a></li>
            </ul>
        </div>
    </div>
@endsection