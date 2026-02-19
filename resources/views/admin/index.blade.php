@extends('layouts.admin')

@section('title', 'Administrador - ContainerCheck')

@section('content')
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm bg-primary text-white overflow-hidden p-4 rounded-4 position-relative">
                    <div class="position-relative z-1">
                        <h2 class="fw-bold mb-1">¡Hola, {{ Auth::user()->name ?? 'Administrador' }}!</h2>
                        <p class="mb-0 opacity-75">Bienvenido al panel central de ContainerCheck. Aquí tienes un resumen de
                            la actividad reciente.</p>
                    </div>
                    <div class="position-absolute end-0 top-0 bottom-0 opacity-25 p-4 d-none d-lg-block">
                        <i class="bi bi-grid-3x3-gap-fill" style="font-size: 8rem;"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100 rounded-3">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-primary bg-opacity-10 p-2 rounded-3 me-3">
                                <i class="bi bi-clipboard-data text-primary fs-4"></i>
                            </div>
                            <h6 class="card-title mb-0">Inspecciones Totales</h6>
                        </div>
                        <h3 class="fw-bold mb-1">{{ number_format($stats['total_inspections']) }}</h3>
                        <p class="text-muted small mb-0">Histórico de inspecciones</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100 rounded-3 border-start border-success border-4">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-success bg-opacity-10 p-2 rounded-3 me-3">
                                <i class="bi bi-check-circle-fill text-success fs-4"></i>
                            </div>
                            <h6 class="card-title mb-0">Completadas</h6>
                        </div>
                        <h3 class="fw-bold mb-1">{{ number_format($stats['completed_inspections']) }}</h3>
                        <p class="text-muted small mb-0">Liberadas satisfactoriamente</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100 rounded-3 border-start border-warning border-4">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-warning bg-opacity-10 p-2 rounded-3 me-3">
                                <i class="bi bi-hourglass-split text-warning fs-4"></i>
                            </div>
                            <h6 class="card-title mb-0">Pendientes</h6>
                        </div>
                        <h3 class="fw-bold mb-1">{{ number_format($stats['pending_inspections']) }}</h3>
                        <p class="text-muted small mb-0">Esperando liberación</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100 rounded-3">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-info bg-opacity-10 p-2 rounded-3 me-3">
                                <i class="bi bi-people-fill text-info fs-4"></i>
                            </div>
                            <h6 class="card-title mb-0">Usuarios</h6>
                        </div>
                        <h3 class="fw-bold mb-1">{{ number_format($stats['total_users']) }}</h3>
                        <p class="text-muted small mb-0">Usuarios registrados</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Recent Activity -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0">Actividad Reciente</h5>
                        <a href="{{ route('admin.reports.index') }}" class="btn btn-sm btn-outline-primary">Ver todos</a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4">Contenedor</th>
                                        <th>Versión</th>
                                        <th>Usuario</th>
                                        <th>Estado</th>
                                        <th>Fecha</th>
                                        <th class="text-end pe-4">Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recent_inspections as $inspection)
                                        <tr>
                                            <td class="ps-4 fw-semibold text-primary">
                                                {{ $inspection->getContainerNumber() }}
                                            </td>
                                            <td>{{ $inspection->formVersion->name ?? 'N/A' }}</td>
                                            <td>{{ $inspection->user->name ?? 'N/A' }}</td>
                                            <td>
                                                @php
                                                    $statusBadge = match ($inspection->status) {
                                                        'completed' => 'bg-success',
                                                        'pending_monitoreo' => 'bg-warning text-dark',
                                                        'rejected' => 'bg-danger',
                                                        'draft' => 'bg-secondary',
                                                        default => 'bg-light text-dark'
                                                    };
                                                    $statusLabel = match ($inspection->status) {
                                                        'completed' => 'Completada',
                                                        'pending_monitoreo' => 'Pendiente',
                                                        'rejected' => 'Rechazada',
                                                        'draft' => 'Borrador',
                                                        default => $inspection->status
                                                    };
                                                @endphp
                                                <span
                                                    class="badge {{ $statusBadge }} rounded-pill px-3">{{ $statusLabel }}</span>
                                            </td>
                                            <td>{{ $inspection->created_at->format('d/m/Y H:i') }}</td>
                                            <td class="text-end pe-4">
                                                <a href="{{ route('admin.reports.show', $inspection) }}"
                                                    class="btn btn-sm btn-icon btn-light">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4 text-muted">No hay inspecciones recientes
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Side Cards -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white py-3 border-0">
                        <h5 class="fw-bold mb-0">Gestión de Acceso</h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            @foreach($users_by_role as $roleStat)
                                <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-light p-2 rounded-circle me-3">
                                            <i class="bi bi-shield-lock-fill text-muted"></i>
                                        </div>
                                        <span class="text-capitalize">{{ str_replace('_', ' ', $roleStat->role) }}</span>
                                    </div>
                                    <span class="badge bg-secondary rounded-pill">{{ $roleStat->total }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-3 bg-dark text-white p-2">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3 d-flex align-items-center">
                            <i class="bi bi-lightning-fill text-warning me-2"></i> Atajos Rápidos
                        </h6>
                        <div class="d-grid gap-2">
                            <a href="{{ route('admin.forms.index') }}" class="btn btn-outline-light btn-sm text-start">
                                <i class="bi bi-gear-fill me-2"></i> Configurar Formularios
                            </a>
                            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-light btn-sm text-start">
                                <i class="bi bi-people-fill me-2"></i> Gestionar Usuarios
                            </a>
                            <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-light btn-sm text-start">
                                <i class="bi bi-file-earmark-text-fill me-2"></i> Gestionar Reportes
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection