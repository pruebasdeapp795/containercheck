@extends('layouts.admin')

@section('title', 'Gestión de Usuarios - Admin')

@section('content')
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="fw-bold mb-1">Gestión de Usuarios</h2>
                    <p class="text-muted mb-0">Administra los accesos y roles del personal.</p>
                </div>
                <a href="{{ route('admin.users.create') }}" class="btn btn-primary shadow-sm">
                    <i class="bi bi-person-plus-fill me-2"></i> Nuevo Usuario
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-muted small text-uppercase fw-bold">
                        <tr>
                            <th class="ps-4 py-3">Nombre / Login</th>
                            <th class="py-3">Cédula</th>
                            <th class="py-3">Email</th>
                            <th class="py-3">Rol</th>
                            <th class="py-3 text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse($users as $user)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary bg-opacity-10 p-2 rounded-3 me-3">
                                            <i class="bi bi-person text-primary"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark">{{ $user->name }}</div>
                                            <div class="small text-muted">@<span></span>{{ $user->usuario }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $user->cedula }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @php
                                        $roleColor = match ($user->role) {
                                            'admin' => 'bg-dark',
                                            'monitoreo' => 'bg-info text-dark',
                                            'control_riesgo' => 'bg-primary',
                                            'despacho' => 'bg-warning text-dark',
                                            'comex' => 'bg-success',
                                            'personal' => 'bg-secondary',
                                            default => 'bg-light text-dark'
                                        };
                                    @endphp
                                    <span class="badge {{ $roleColor }} rounded-pill px-3 py-2 text-capitalize">
                                        {{ str_replace('_', ' ', $user->role) }}
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="btn-group shadow-sm rounded-3">
                                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-white btn-sm px-3"
                                            title="Editar">
                                            <i class="bi bi-pencil-square text-primary"></i>
                                        </a>
                                        @if($user->id !== auth()->id())
                                            <button type="button" class="btn btn-white btn-sm px-3" title="Eliminar"
                                                onclick="confirmDelete({{ $user->id }}, '{{ $user->name }}')">
                                                <i class="bi bi-trash3 text-danger"></i>
                                            </button>
                                            <form id="delete-form-{{ $user->id }}"
                                                action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-none">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="bi bi-people fs-1 d-block mb-3 opacity-25"></i>
                                        No hay usuarios registrados.
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($users->hasPages())
                <div class="card-footer bg-white py-3 border-0">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>

    <script>
        function confirmDelete(id, name) {
            if (confirm('¿Estás seguro de que deseas eliminar al usuario "' + name + '"?')) {
                document.getElementById('delete-form-' + id).submit();
            }
        }
    </script>
@endsection