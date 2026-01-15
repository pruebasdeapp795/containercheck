@extends('layouts.app')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-bold text-gray-800">Panel de Inspector</h2>
    <form action="{{ route('inspector.inspections.store') }}" method="POST">
        @csrf
        <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded shadow-lg transform transition hover:scale-105">
            <i class="fas fa-plus-circle"></i> Nueva Inspección
        </button>
    </form>
</div>

<div class="bg-white shadow-md rounded my-6 overflow-hidden">
    <div class="overflow-x-auto"> <table class="min-w-full leading-normal">
            <thead>
                <tr>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        ID
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Estado
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Participante
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Creación
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Acciones
                    </th>
                </tr>
            </thead>
            <tbody>
                @forelse($inspections as $inspection)
                    <tr>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            <p class="text-gray-900 whitespace-no-wrap">#{{ $inspection->id }}</p>
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            <span class="relative inline-block px-3 py-1 font-semibold leading-tight">
                                <span aria-hidden class="absolute inset-0 opacity-50 rounded-full 
                                    @if($inspection->status == 'completed') bg-green-200 
                                    @elseif($inspection->status == 'pending_signature') bg-yellow-200
                                    @elseif($inspection->status == 'expired') bg-red-200
                                    @else bg-blue-200 @endif
                                "></span>
                                <span class="relative text-gray-900">
                                    {{ ucfirst(str_replace('_', ' ', $inspection->status)) }}
                                </span>
                            </span>
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            <p class="text-gray-900 whitespace-no-wrap">{{ $inspection->participant->name ?? 'Sin asignar' }}</p>
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            <p class="text-gray-900 whitespace-no-wrap">{{ $inspection->created_at->format('d/m/Y H:i') }}</p>
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            <div class="flex gap-2">
                                <a href="{{ route('inspector.inspections.edit', $inspection->id) }}" class="text-blue-600 hover:text-blue-900">
                                    {{ $inspection->status == 'completed' || $inspection->status == 'pending_signature' ? 'Ver' : 'Continuar' }}
                                </a>
                                @if($inspection->status == 'completed')
                                    <a href="{{ route('pdf.inspection', $inspection->id) }}" class="text-red-600 hover:text-red-900">
                                        <i class="fas fa-file-pdf"></i> PDF
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-center">
                            No tienes inspecciones activas.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div> <div class="px-5 py-5 bg-white border-t flex flex-col xs:flex-row items-center xs:justify-between">
        {{ $inspections->links() }}
    </div>
</div>
@endsection