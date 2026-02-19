<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Despacho</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
        }

        .navbar {
            background-color: #ffffff !important;
            border-bottom: 1px solid #eee;
            padding: 0.8rem 2rem;
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .nav-link.active {
            color: #0d6efd !important;
            font-weight: 700;
        }

        .stat-card {
            background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
            color: white;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('despacho.index') }}">
                <img src="{{ asset('imagenes/containerchecklogov.png') }}" width="140px" alt="ContainerCheck">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navContent">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navContent">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link active" href="{{ route('despacho.index') }}">Inicio</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('despacho.index') }}">Inspecciones</a>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('despacho.reportes') }}">Reportes</a>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('despacho.inventario') }}">Inventario</a>
                    </li>
                    <li class="nav-item">
                        <form action="{{ route('logout') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-link nav-link text-danger">Cerrar Sesión</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="row mb-4">
            <div class="col-12 text-center text-md-start">
                <h2 class="fw-bold">Bienvenido, {{ Auth::user()->name }}</h2>
                <p class="text-muted">Gestión de inspecciones e inventario de despacho.</p>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($openInspections->count() > 0)
            <div class="row mb-5">
                <div class="col-12">
                    <div class="card border-warning shadow-sm">
                        <div class="card-header bg-warning bg-opacity-10 text-dark fw-bold">
                            <i class="bi bi-clock-history me-2"></i> Inspecciones Abiertas (Borradores)
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th>Formulario</th>
                                            <th>Usuario</th>
                                            <th>Iniciado el</th>
                                            <th>Progreso</th>
                                            <th>Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($openInspections as $draft)
                                            <tr>
                                                <td>{{ $draft->formVersion->version }}</td>
                                                <td>
                                                    <span class="badge bg-light text-dark fw-normal border">
                                                        <i class="bi bi-person me-1"></i>{{ $draft->user->name ?? 'N/A' }}
                                                    </span>
                                                </td>
                                                <td>{{ $draft->created_at->format('d/m/Y H:i') }}</td>
                                                <td>
                                                    @php
                                                        $totalPhases = $draft->formVersion->phases->count();
                                                        $completed = $draft->last_phase_completed + 1;
                                                        $perc = ($totalPhases > 0) ? ($completed / $totalPhases) * 100 : 0;
                                                    @endphp
                                                    <div class="progress" style="height: 10px;">
                                                        <div class="progress-bar bg-info" role="progressbar"
                                                            style="width: {{ $perc }}%"></div>
                                                    </div>
                                                    <small class="text-muted">{{ $completed }} de {{ $totalPhases }}
                                                        fases</small>
                                                </td>
                                                <td>
                                                    @if($draft->user_id === Auth::id())
                                                        <a href="{{ route('despacho.inspecciones.edit', $draft->id) }}"
                                                            class="btn btn-primary btn-sm rounded-pill">
                                                            Continuar <i class="bi bi-arrow-right"></i>
                                                        </a>
                                                    @else
                                                        <button class="btn btn-secondary btn-sm rounded-pill opacity-50" disabled
                                                            title="Solo el creador puede continuar">
                                                            Continuar <i class="bi bi-lock"></i>
                                                        </button>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="row g-4">
            <div class="col-md-4">
                <div class="card stat-card p-4 h-100 d-flex flex-column justify-content-center shadow">
                    <h6 class="text-uppercase small fw-bold mb-3 opacity-75">Inspecciones Finalizadas</h6>
                    <h2 class="display-4 fw-bold mb-0">{{ $totalInspections }}</h2>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card p-4 h-100 shadow-sm border-0">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold m-0">Últimas Inspecciones</h5>
                        <a href="{{ route('despacho.reportes') }}"
                            class="btn btn-sm btn-link text-decoration-none p-0">Ver historial</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Versión</th>
                                    <th>Creado por</th>
                                    <th>Fecha</th>
                                    <th>Estado</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentInspections as $ri)
                                    <tr>
                                        <td class="fw-semibold">{{ $ri->formVersion->version }}</td>
                                        <td>{{ $ri->user->name ?? 'N/A' }}</td>
                                        <td>{{ $ri->created_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            @if($ri->status === 'completed')
                                                <span class="badge bg-success">Completada</span>
                                            @elseif($ri->status === 'pending_monitoreo')
                                                <span class="badge bg-warning">Pendiente Monitoreo</span>
                                            @elseif($ri->status === 'rejected')
                                                <span class="badge bg-danger">Rechazada</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('despacho.show', $ri->id) }}"
                                                class="btn btn-sm btn-outline-primary shadow-sm rounded-3">
                                                <i class="bi bi-eye"></i> Ver
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-5">
            <h4 class="fw-bold mb-3 text-center">Empezar Nueva Inspección</h4>
            <div class="row justify-content-center g-3">
                @forelse($availableVersions as $version)
                    <div class="col-md-4">
                        <div class="card text-center p-4 border-0 shadow-sm hover-card">
                            <div class="mb-3">
                                <i class="bi bi-file-earmark-text text-primary" style="font-size: 2.5rem;"></i>
                            </div>
                            <h5 class="fw-bold">{{ $version->version }}</h5>
                            <p class="text-muted small">Haz clic para iniciar el proceso de captura de datos.</p>
                            <a href="{{ route('despacho.inspecciones.create', $version->id) }}"
                                class="btn btn-primary w-100 rounded-pill py-2">
                                INICIAR AHORA
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center text-muted">No hay formularios configurados actualmente.</div>
                @endforelse
            </div>
        </div>
    </div>

    <style>
        .hover-card:hover {
            transform: translateY(-5px);
            transition: all 0.3s ease;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>