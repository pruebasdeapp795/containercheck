<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Reportes - Control de Riesgo</title>
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
            color: #8a70d6 !important;
            font-weight: 700;
        }

        .badge-success {
            background-color: #d1fae5;
            color: #065f46;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('admin.index') }}">
                <img src="{{ asset('imagenes/containerchecklogov.png') }}" width="140px" alt="ContainerCheck">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navContent">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navContent">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="{{ route('control-riesgo.index') }}">Inicio</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('control-riesgo.index') }}">Inspecciones</a>
                    </li>
                    <li class="nav-item"><a class="nav-link active"
                            href="{{ route('control-riesgo.reportes') }}">Reportes</a></li>
                    <li class="nav-item"><a class="nav-link text-success fw-bold"
                            href="{{ asset('formats/formato_inspeccion.xlsx') }}" download>
                            <i class="bi bi-file-earmark-excel me-1"></i> Descargar Formato
                        </a></li>
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
        <div class="row mb-4 align-items-center">
            <div class="col">
                <h2>Mis Reportes</h2>
                <p class="text-muted">Historial de inspecciones realizadas.</p>
            </div>
            <div class="col-auto">
                <a href="{{ route('control-riesgo.index') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg"></i> Nueva
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Fecha y Hora</th>
                            <th>Formulario</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($responses as $response)
                            <tr>
                                <td>{{ $response->created_at->format('d/m/Y H:i') }}</td>
                                <td>{{ $response->formVersion->version }}</td>
                                <td>
                                    @if($response->status == 'completed')
                                        <span class="badge badge-success px-3 py-2 rounded-pill">Completado</span>
                                    @elseif($response->status == 'rejected')
                                        <span class="badge bg-danger px-3 py-2 rounded-pill text-white">Rechazado</span>
                                    @else
                                        <span class="badge bg-warning px-3 py-2 rounded-pill text-dark">Borrador</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('control-riesgo.reportes.show', $response->id) }}"
                                        class="btn btn-sm btn-outline-primary px-3">
                                        <i class="bi bi-eye"></i> Detalle
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">Aún no has realizado ninguna inspección.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>