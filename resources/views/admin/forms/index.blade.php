@extends('layouts.admin')

@section('title', 'Configuración de Formularios - Admin')

@push('styles')
    <style>
        .btn-primary {
            background-color: #8a70d6;
            border: none;
        }

        .btn-primary:hover {
            background-color: #7b62c4;
        }

        .badge-active {
            background-color: #e8f5e9;
            color: #2e7d32;
        }
    </style>
@endpush

@section('content')
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Versiones del Formulario</h2>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newVersionModal">
                <i class="bi bi-plus-lg"></i> Nueva Versión
            </button>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card p-4">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Versión</th>
                        <th>Estado</th>
                        <th>Fecha Creación</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($versions as $v)
                        <tr>
                            <td class="fw-bold">{{ $v->version }}</td>
                            <td>
                                @if($v->is_active)
                                    <span class="badge badge-active p-2">Activo</span>
                                @else
                                    <span class="badge bg-light text-muted p-2">Inactivo</span>
                                @endif
                            </td>
                            <td>{{ $v->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <a href="{{ route('admin.forms.show', $v->id) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-gear"></i> Configurar
                                </a>
                                <form action="{{ route('admin.forms.duplicate', $v->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-primary" title="Copiar esta versión">
                                        <i class="bi bi-copy"></i> Copiar
                                    </button>
                                </form>
                                @if(!$v->is_active)
                                    <form action="{{ route('admin.forms.activate', $v->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-success">Activar</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="newVersionModal" tabindex="-1">
        <div class="modal-dialog">
            <form action="{{ route('admin.forms.version.store') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Nueva Versión</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nombre de la Versión</label>
                            <input type="text" name="version" class="form-control" placeholder="Ej: V1.0" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Crear</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection