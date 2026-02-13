<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventario - Despacho</title>
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
                    <li class="nav-item"><a class="nav-link" href="{{ route('despacho.index') }}">Inicio</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('despacho.index') }}">Inspecciones</a></li>
                    <li class="nav-item"><a class="nav-link active"
                            href="{{ route('despacho.inventario') }}">Inventario</a></li>
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
            <div class="col-12">
                <h2 class="fw-bold">Inventario de Precintos</h2>
                <p class="text-muted">Gestión de precintos disponibles en despacho.</p>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-plus-circle me-2"></i>Agregar Precinto</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('despacho.inventario.store') }}" method="POST">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Código</label>
                                    <input type="text" name="codigo" class="form-control" required
                                        placeholder="Ej: PRE-001">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Tipo</label>
                                    <select name="tipo" class="form-select" required>
                                        <option value="">Seleccione...</option>
                                        <option value="Metálico">Metálico</option>
                                        <option value="Plástico">Plástico</option>
                                        <option value="Cable">Cable</option>
                                        <option value="Electrónico">Electrónico</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Cantidad</label>
                                    <input type="number" name="cantidad" class="form-control" min="1" required
                                        placeholder="0">
                                </div>
                            </div>
                            <div class="mt-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save me-2"></i>Agregar Precinto
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="bi bi-list-ul me-2"></i>Lista de Precintos</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Código</th>
                                        <th>Tipo</th>
                                        <th>Cantidad</th>
                                        <th>Fecha Ingreso</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($precintos as $precinto)
                                        <tr>
                                            <td class="fw-semibold">#{{ $precinto->id }}</td>
                                            <td>{{ $precinto->codigo }}</td>
                                            <td>
                                                <span class="badge bg-secondary">{{ $precinto->tipo }}</span>
                                            </td>
                                            <td>{{ $precinto->cantidad }}</td>
                                            <td>{{ $precinto->fecha_ingreso->format('d/m/Y') }}</td>
                                            <td>
                                                @if($precinto->estado === 'disponible')
                                                    <span class="badge bg-success">Disponible</span>
                                                @elseif($precinto->estado === 'en_uso')
                                                    <span class="badge bg-warning">En Uso</span>
                                                @else
                                                    <span class="badge bg-danger">Agotado</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-4">
                                                No hay precintos registrados.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>