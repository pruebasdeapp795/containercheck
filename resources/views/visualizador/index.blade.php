<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Control Riesgo - ContainerCheck</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
        }

        .navbar {
            background-color: #ffffff;
            border-bottom: 1px solid #eee;
            padding: 0.8rem 2rem;
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            margin-bottom: 1.5rem;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <img src="{{ asset('imagenes/containerchecklogov.png') }}" width="140px" alt="ContainerCheck">
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarMain">
                <div class="ms-auto d-flex flex-column flex-lg-row align-items-lg-center gap-3 mt-3 mt-lg-0">
                    <span class="badge bg-secondary text-white">Perfil: Control Riesgo</span>
                    <a href="{{ route('control-riesgo.inventario') }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-box-seam me-1"></i>Inventario
                    </a>
                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-danger w-100">
                            <i class="bi bi-box-arrow-right me-1"></i>Cerrar Sesión
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h2 class="fw-bold mb-4">Inspecciones Recientes (Solo Lectura)</h2>

        <div class="card p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Fecha</th>
                            <th>ID</th>
                            <th>Realizado Por</th>
                            <th>Autorizado Por</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($responses as $response)
                            <tr>
                                <td>{{ $response->created_at->format('d/m/Y H:i') }}</td>
                                <td>#{{ $response->id }}</td>
                                <td>{{ $response->user->name ?? '-' }}</td>
                                <td>{{ $response->monitoreoUser->name ?? '-' }}</td>
                                <td>
                                    @if($response->status == 'completed')
                                        <span class="badge bg-success">Completado</span>
                                    @elseif($response->status == 'pending_monitoreo')
                                        <span class="badge bg-warning text-dark">Pendiente Firma</span>
                                    @elseif($response->status == 'rejected')
                                        <span class="badge bg-danger">Rechazado</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $response->status }}</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('control-riesgo.show', $response->id) }}"
                                        class="btn btn-sm btn-outline-primary px-3">
                                        <i class="bi bi-eye"></i> Ver Detalle
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">No hay registros.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="d-flex justify-content-center">
                    {{ $responses->links() }}
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>