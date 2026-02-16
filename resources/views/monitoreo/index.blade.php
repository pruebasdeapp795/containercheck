<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoreo - ContainerCheck</title>
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
            margin-bottom: 1.5rem;
        }

        .nav-link.active {
            color: #8a70d6 !important;
            font-weight: 700;
        }

        .section-title {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: #333;
            border-left: 5px solid #8a70d6;
            padding-left: 10px;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <img src="{{ asset('imagenes/containerchecklogov.png') }}" width="140px" alt="ContainerCheck">
            </a>
            <div class="d-flex align-items-center">
                <span class="badge bg-secondary text-white me-3">Perfil: Monitoreo</span>

                @if(Auth::user()->saved_signature)
                    <span class="badge bg-success text-white me-2">
                        <i class="bi bi-check-circle-fill"></i> Firma Cargada
                    </span>
                    <button class="btn btn-sm btn-outline-primary me-2" data-bs-toggle="modal"
                        data-bs-target="#signatureModal">
                        <i class="bi bi-pencil"></i> Actualizar Firma
                    </button>
                @else
                    <span class="badge bg-warning text-dark me-2">
                        <i class="bi bi-exclamation-triangle-fill"></i> Sin Firma
                    </span>
                    <button class="btn btn-sm btn-primary me-2" data-bs-toggle="modal" data-bs-target="#signatureModal">
                        <i class="bi bi-upload"></i> Cargar Firma
                    </button>
                @endif

                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-danger">Cerrar Sesión</button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="row align-items-center mb-4">
            <div class="col">
                <h2 class="fw-bold">Panel de Monitoreo</h2>
                <p class="text-muted">Aprobación y Revisión de Inspecciones</p>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('warning'))
            <div class="alert alert-warning alert-dismissible fade show">
                <i class="bi bi-exclamation-triangle me-2"></i>
                {{ session('warning') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(!Auth::user()->saved_signature)
            <div class="alert alert-warning border-warning shadow-sm">
                <h5 class="alert-heading fw-bold text-warning-emphasis">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>Firma Requerida
                </h5>
                <p class="mb-2">Debe cargar su firma antes de poder liberar inspecciones.</p>
                <button class="btn btn-warning fw-bold" data-bs-toggle="modal" data-bs-target="#signatureModal">
                    <i class="bi bi-upload me-2"></i>Cargar Mi Firma Ahora
                </button>
            </div>
        @endif

        <h3 class="section-title text-warning">Pendientes de Liberación</h3>
        <div class="card p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Fecha</th>
                            <th>Inspección ID</th>
                            <th>Realizado Por</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pending as $response)
                            <tr>
                                <td>{{ $response->created_at->format('d/m/Y H:i') }}</td>
                                <td>#{{ $response->id }}</td>
                                <td>{{ $response->user->name ?? 'Desconocido' }}</td>
                                <td><span class="badge bg-warning text-dark">Pendiente Firma</span></td>
                                <td>
                                    <a href="{{ route('monitoreo.show', $response->id) }}"
                                        class="btn btn-sm btn-primary fw-bold px-3">
                                        <i class="bi bi-pen-fill"></i> Revisar y Firmar
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">No hay inspecciones pendientes por
                                    liberar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <h3 class="section-title text-success mt-5">Historial Liberado</h3>
        <div class="card p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Fecha</th>
                            <th>Inspección ID</th>
                            <th>Realizado Por</th>
                            <th>Liberado Por</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($completed as $response)
                            <tr>
                                <td>{{ $response->created_at->format('d/m/Y H:i') }}</td>
                                <td>#{{ $response->id }}</td>
                                <td>{{ $response->user->name ?? '-' }}</td>
                                <td>{{ $response->monitoreoUser->name ?? '-' }}</td>
                                <td><span class="badge bg-success">Completado</span></td>
                                <td>
                                    <a href="{{ route('monitoreo.show', $response->id) }}"
                                        class="btn btn-sm btn-outline-secondary px-3">
                                        <i class="bi bi-eye"></i> Ver
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">No hay inspecciones completadas
                                    recientes.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Signature Upload Modal -->
    <div class="modal fade" id="signatureModal" tabindex="-1" aria-labelledby="signatureModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="signatureModalLabel">
                        <i class="bi bi-upload me-2"></i>{{ Auth::user()->saved_signature ? 'Actualizar' : 'Cargar' }}
                        Firma
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form action="{{ route('monitoreo.uploadSignature') }}" method="POST" enctype="multipart/form-data"
                    id="signatureForm">
                    @csrf
                    <div class="modal-body">
                        <p class="text-muted small mb-3">
                            <i class="bi bi-info-circle me-1"></i>
                            Suba una imagen de su firma (PNG, JPG o JPEG). Esta firma se usará automáticamente al
                            liberar inspecciones.
                        </p>

                        @if(Auth::user()->saved_signature)
                            <div class="alert alert-info small">
                                <i class="bi bi-check-circle me-1"></i>
                                Ya tiene una firma guardada. Al subir una nueva, se reemplazará.
                            </div>

                            <div class="mb-3 p-3 bg-light border rounded text-center">
                                <small class="text-muted d-block mb-2">Firma actual:</small>
                                <img src="{{ Auth::user()->saved_signature }}"
                                    style="max-height: 100px; border: 1px solid #ddd; padding: 5px; background: white;">
                            </div>
                        @endif

                        <div class="mb-3">
                            <label for="signatureImage" class="form-label fw-semibold">
                                <i class="bi bi-image me-1"></i>Seleccionar imagen de firma
                            </label>
                            <input type="file" class="form-control" id="signatureImage" name="signature_image"
                                accept="image/png,image/jpeg,image/jpg" required onchange="previewSignature(this)">
                            <small class="text-muted">Formatos: PNG, JPG, JPEG. Tamaño máximo: 2MB</small>
                        </div>

                        <div id="imagePreview" class="d-none">
                            <label class="form-label fw-semibold">Vista previa:</label>
                            <div class="p-3 bg-light border rounded text-center">
                                <img id="previewImg" src="" style="max-height: 150px; max-width: 100%;">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-upload me-2"></i>Cargar Firma
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function previewSignature(input) {
            const preview = document.getElementById('imagePreview');
            const previewImg = document.getElementById('previewImg');

            if (input.files && input.files[0]) {
                const reader = new FileReader();

                reader.onload = function (e) {
                    previewImg.src = e.target.result;
                    preview.classList.remove('d-none');
                };

                reader.readAsDataURL(input.files[0]);
            } else {
                preview.classList.add('d-none');
            }
        }
    </script>
</body>

</html>