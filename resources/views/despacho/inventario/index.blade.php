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

        .nav-tabs .nav-link {
            transition: all 0.2s ease;
            color: #718096;
            border: none;
            border-bottom: 3px solid transparent;
        }

        .nav-tabs .nav-link:hover {
            color: #0d6efd;
            border-bottom: 3px solid #e2e8f0;
        }

        .nav-tabs .nav-link.active {
            color: #0d6efd !important;
            font-weight: 700;
            border-bottom: 3px solid #0d6efd !important;
            background: rgba(13, 110, 253, 0.05) !important;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(13, 110, 253, 0.02);
        }

        .badge {
            font-weight: 600;
            padding: 0.5em 0.8em;
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
        <div class="row mb-4 align-items-center">
            <div class="col-md-6 text-start">
                <h2 class="fw-bold m-0">Inventario en Logística</h2>
                <p class="text-muted m-0">Precintos recibidos y disponibles para despacho.</p>
            </div>
            <div class="col-md-6 mt-3 mt-md-0">
                <form action="{{ route('despacho.inventario') }}" method="GET" class="d-flex">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
                        <input type="text" name="q" class="form-control border-start-0"
                            placeholder="Buscar por código de precinto..." value="{{ $search }}">
                        <button class="btn btn-primary px-4" type="submit">Buscar</button>
                    </div>
                    @if($search)
                        <a href="{{ route('despacho.inventario') }}" class="btn btn-outline-secondary ms-2"><i
                                class="bi bi-x-lg"></i></a>
                    @endif
                </form>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card shadow-sm overflow-hidden">
            <div class="card-header bg-white p-0 border-bottom">
                <ul class="nav nav-tabs border-0" id="despachoTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active px-4 py-3 fw-bold border-0 rounded-0" id="disponibles-tab"
                            data-bs-toggle="tab" data-bs-target="#disponibles" type="button" role="tab"
                            onclick="setTab('disponibles')">
                            <i class="bi bi-check-circle me-2"></i>Disponibles ({{ $enLogistica->total() }})
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link px-4 py-3 fw-bold border-0 rounded-0" id="historial-tab"
                            data-bs-toggle="tab" data-bs-target="#historial" type="button" role="tab"
                            onclick="setTab('historial')">
                            <i class="bi bi-clock-history me-2"></i>Traslados Recibidos
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link px-4 py-3 fw-bold border-0 rounded-0" id="usados-tab"
                            data-bs-toggle="tab" data-bs-target="#usados" type="button" role="tab"
                            onclick="setTab('usados')">
                            <i class="bi bi-archive me-2"></i>Historial de Uso ({{ $usados->total() }})
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link px-4 py-3 fw-bold border-0 rounded-0" id="anulados-tab"
                            data-bs-toggle="tab" data-bs-target="#anulados" type="button" role="tab"
                            onclick="setTab('anulados')">
                            <i class="bi bi-x-octagon me-2"></i>Anulados ({{ $anulados->total() }})
                        </button>
                    </li>
                </ul>
            </div>
            <div class="card-body p-0">
                <div class="tab-content" id="despachoTabsContent">
                    {{-- Disponibles --}}
                    <div class="tab-pane fade show active" id="disponibles" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4">Código</th>
                                        <th>Tipo</th>
                                        <th>Fecha Recibido</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($enLogistica as $precinto)
                                        <tr>
                                            <td class="ps-4 fw-bold text-primary">{{ $precinto->codigo }}</td>
                                            <td><span class="badge bg-secondary">{{ $precinto->tipo }}</span></td>
                                            <td>{{ $precinto->updated_at->format('d/m/Y H:i') }}</td>
                                            <td><span class="badge bg-warning text-dark small"><i
                                                        class="bi bi-geo-alt me-1"></i>En Logística</span></td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-5 text-muted">No hay precintos disponibles
                                                en este momento.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="p-3 border-top">
                            {{ $enLogistica->appends(['q' => $search, 'tab' => 'disponibles'])->links() }}
                        </div>
                    </div>

                    {{-- Historial de Traslados --}}
                    <div class="tab-pane fade" id="historial" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4">Fecha</th>
                                        <th>Tipo</th>
                                        <th>Cantidad</th>
                                        <th>Enviado por</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($transferencias as $t)
                                        <tr>
                                            <td class="ps-4">{{ $t->fecha_traslado->format('d/m/Y H:i') }}</td>
                                            <td><span class="badge bg-info text-dark">{{ $t->tipo }}</span></td>
                                            <td class="fw-bold">{{ $t->cantidad }} unidades</td>
                                            <td>{{ $t->user->name }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-4 text-muted">No se han recibido traslados
                                                aún.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="p-3 border-top">
                            {{ $transferencias->appends(['q' => $search, 'tab' => 'historial'])->links() }}
                        </div>
                    </div>

                    {{-- Historial de Uso --}}
                    <div class="tab-pane fade" id="usados" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4">Código</th>
                                        <th>Tipo</th>
                                        <th>Fecha Uso</th>
                                        <th>Contenedor</th>
                                        <th>Inspección</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($usados as $precinto)
                                        <tr>
                                            <td class="ps-4 fw-bold text-muted">{{ $precinto->codigo }}</td>
                                            <td><span
                                                    class="badge bg-dark bg-opacity-10 text-dark">{{ $precinto->tipo }}</span>
                                            </td>
                                            <td>{{ $precinto->usado_at ? $precinto->usado_at->format('d/m/Y H:i') : '-' }}
                                            </td>
                                            <td><span
                                                    class="text-danger fw-bold fs-5">{{ $precinto->numero_contenedor ?? '-' }}</span>
                                            </td>
                                            <td><a href="{{ route('despacho.show', $precinto->form_response_id) }}"
                                                    class="btn btn-sm btn-link">#{{ $precinto->form_response_id }}</a></td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-5 text-muted">No hay registros de uso
                                                recientes.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="p-3 border-top">
                            {{ $usados->appends(['q' => $search, 'tab' => 'usados'])->links() }}
                        </div>
                    </div>

                    {{-- Anulados --}}
                    <div class="tab-pane fade" id="anulados" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4">Código</th>
                                        <th>Tipo</th>
                                        <th>Fecha Anulación</th>
                                        <th>Motivo (Rechazo)</th>
                                        <th>Inspección</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($anulados as $precinto)
                                        <tr>
                                            <td class="ps-4 fw-bold text-danger">{{ $precinto->codigo }}</td>
                                            <td><span
                                                    class="badge bg-danger bg-opacity-10 text-danger text-dark">{{ $precinto->tipo }}</span>
                                            </td>
                                            <td>{{ $precinto->updated_at->format('d/m/Y H:i') }}</td>
                                            <td class="small text-muted">
                                                @if($precinto->formResponse)
                                                    {{ Str::limit($precinto->formResponse->rejection_reason, 50) }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                @if($precinto->form_response_id)
                                                    <a href="{{ route('despacho.show', $precinto->form_response_id) }}"
                                                        class="btn btn-sm btn-link text-danger">#{{ $precinto->form_response_id }}</a>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-5 text-muted">No hay registros de
                                                precintos anulados.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="p-3 border-top">
                            {{ $anulados->appends(['q' => $search, 'tab' => 'anulados'])->links() }}
                        </div>
                    </div>

                    {{-- Todos los Ingresos --}}
                 
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Set tab in URL to preserve state on refresh/pagination
        function setTab(tabName) {
            const url = new URL(window.location);
            url.searchParams.set('tab', tabName);
            window.history.replaceState({}, '', url);
        }

        // Activate tab from URL on load
        document.addEventListener('DOMContentLoaded', function () {
            const urlParams = new URLSearchParams(window.location.search);
            const activeTab = urlParams.get('tab');
            if (activeTab) {
                const tabEl = document.querySelector(`#${activeTab}-tab`);
                if (tabEl) {
                    const tab = new bootstrap.Tab(tabEl);
                    tab.show();
                }
            }
        });
    </script>
</body>

</html>