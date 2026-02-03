<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurar Versión {{ $version->version }}</title>
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
            margin-bottom: 20px;
        }

        .btn-primary {
            background-color: #8a70d6;
            border: none;
        }

        .phase-header {
            background-color: #f1f0ff;
            padding: 15px;
            border-radius: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .field-item {
            padding: 10px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .field-item:last-child {
            border-bottom: none;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="{{ route('admin.index') }}">ContainerCheck</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="{{ route('admin.forms.index') }}">Volver a
                            Versiones</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Configurar Versión: <span class="text-primary">{{ $version->version }}</span></h2>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newPhaseModal">
                <i class="bi bi-plus-lg"></i> Añadir Fase
            </button>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @foreach($version->phases as $phase)
            <div class="card p-3">
                <div class="phase-header mb-3">
                    <h4 class="m-0">{{ $phase->name }}
                        @if(!$phase->is_visible) <small class="text-muted">(Oculto)</small> @endif
                    </h4>
                    <div>
                        <button class="btn btn-sm btn-link" data-bs-toggle="modal"
                            data-bs-target="#editPhase{{ $phase->id }}">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal"
                            data-bs-target="#newField{{ $phase->id }}">
                            <i class="bi bi-plus"></i> Campo
                        </button>
                    </div>
                </div>

                <div class="field-list">
                    @forelse($phase->fields as $field)
                        <div class="field-item">
                            <span>
                                <strong>{{ $field->label }}</strong>
                                <span class="badge bg-light text-dark ms-2">{{ $field->type }}</span>
                                @if(!$field->is_visible) <small class="text-muted">(Oculto)</small> @endif
                            </span>
                            <button class="btn btn-sm btn-link" data-bs-toggle="modal"
                                data-bs-target="#editField{{ $field->id }}">
                                <i class="bi bi-pencil"></i>
                            </button>
                        </div>

                        <!-- Modal Edit Field -->
                        <div class="modal fade" id="editField{{ $field->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <form action="{{ route('admin.forms.field.update', $field->id) }}" method="POST">
                                    @csrf @method('PUT')
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5>Editar Campo</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label">Etiqueta</label>
                                                <input type="text" name="label" class="form-control" value="{{ $field->label }}"
                                                    required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Tipo</label>
                                                <select name="type" class="form-select">
                                                    <option value="text" {{ $field->type == 'text' ? 'selected' : '' }}>Texto</option>
                                                    <option value="numeric" {{ $field->type == 'numeric' ? 'selected' : '' }}>Numérico</option>
                                                    <option value="date" {{ $field->type == 'date' ? 'selected' : '' }}>Fecha</option>
                                                    <option value="photo" {{ $field->type == 'photo' ? 'selected' : '' }}>Foto</option>
                                                    <option value="select" {{ $field->type == 'select' ? 'selected' : '' }}>Selección</option>
                                                </select>
                                            </div>
                                            <div class="mb-3 options-container" style="{{ $field->type == 'select' ? '' : 'display:none' }}">
                                                <label class="form-label">Opciones (separadas por coma)</label>
                                                <textarea name="options" class="form-control"
                                                    placeholder="Opción 1, Opción 2, Opción 3">{{ $field->options }}</textarea>
                                            </div>
                                            <div class="mb-3 rejection-container" style="{{ $field->type == 'select' ? '' : 'display:none' }}">
                                                <label class="form-label text-danger fw-bold">Valor de Rechazo (Opcional)</label>
                                                <input type="text" name="rejection_value" class="form-control"
                                                    placeholder="Si se elige este valor, el formulario se detiene"
                                                    value="{{ $field->rejection_value }}">
                                                <small class="text-muted">Si el usuario selecciona este valor exacto, la inspección se cancelará automáticamente.</small>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="is_visible" value="1"
                                                    id="visField{{ $field->id }}" {{ $field->is_visible ? 'checked' : '' }}>
                                                <label class="form-check-label" for="visField{{ $field->id }}">Visible</label>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-muted m-0 p-3">No hay campos en esta fase.</p>
                    @endforelse
                </div>
            </div>

            <!-- Modal Edit Phase -->
            <div class="modal fade" id="editPhase{{ $phase->id }}" tabindex="-1">
                <div class="modal-dialog">
                    <form action="{{ route('admin.forms.phase.update', $phase->id) }}" method="POST">
                        @csrf @method('PUT')
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5>Editar Fase</h5><button type="button" class="btn-close"
                                    data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label">Nombre de la Fase</label>
                                    <input type="text" name="name" class="form-control" value="{{ $phase->name }}" required>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_visible" value="1"
                                        id="vis{{ $phase->id }}" {{ $phase->is_visible ? 'checked' : '' }}>
                                    <label class="form-check-label" for="vis{{ $phase->id }}">Visible</label>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Modal New Field -->
            <div class="modal fade" id="newField{{ $phase->id }}" tabindex="-1">
                <div class="modal-dialog">
                    <form action="{{ route('admin.forms.field.store', $phase->id) }}" method="POST">
                        @csrf
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5>Nuevo Campo en {{ $phase->name }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label">Etiqueta del Campo</label>
                                    <input type="text" name="label" class="form-control" placeholder="Ej: Humedad del suelo"
                                        required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Tipo de Campo</label>
                                    <select name="type" class="form-select">
                                        <option value="text">Texto</option>
                                        <option value="numeric">Numérico</option>
                                        <option value="date">Fecha</option>
                                        <option value="photo">Foto</option>
                                        <option value="select">Selección</option>
                                    </select>
                                </div>
                                <div class="mb-3 options-container" style="display:none">
                                    <label class="form-label">Opciones (separadas por coma)</label>
                                    <textarea name="options" class="form-control"
                                        placeholder="Opción 1, Opción 2, Opción 3"></textarea>
                                </div>
                                <div class="mb-3 rejection-container" style="display:none">
                                    <label class="form-label text-danger fw-bold">Valor de Rechazo (Opcional)</label>
                                    <input type="text" name="rejection_value" class="form-control" placeholder="Ej: No">
                                    <small class="text-muted">Si el usuario selecciona este valor exacto, la inspección se cancelará automáticamente y se notificará.</small>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">Añadir</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Modal New Phase -->
    <div class="modal fade" id="newPhaseModal" tabindex="-1">
        <div class="modal-dialog">
            <form action="{{ route('admin.forms.phase.store', $version->id) }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5>Nueva Fase</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="text" name="name" class="form-control"
                            placeholder="Nombre de la fase (Ej: Preparación)" required>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Crear</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('change', function (e) {
            if (e.target.matches('select[name="type"]')) {
                const modalBody = e.target.closest('.modal-body');
                const optContainer = modalBody.querySelector('.options-container');
                const rejContainer = modalBody.querySelector('.rejection-container');
                if (e.target.value === 'select') {
                    optContainer.style.display = 'block';
                    rejContainer.style.display = 'block';
                } else {
                    optContainer.style.display = 'none';
                    rejContainer.style.display = 'none';
                }
            }
        });
    </script>
</body>

</html>