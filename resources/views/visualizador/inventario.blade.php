@extends('layouts.admin')

@section('content')
    <style>
        .card-custom {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            background: white;
        }
        .stat-value {
            font-size: 1.8rem;
            font-weight: 800;
            color: #2d3748;
        }
        .text-muted-custom {
            color: #718096;
        }
        .nav-tabs .nav-link {
            color: #718096;
            border: none;
            border-bottom: 3px solid transparent;
        }
        .nav-tabs .nav-link:hover {
            color: #8a70d6;
            border-bottom: 3px solid #e2e8f0;
        }
        .nav-tabs .nav-link.active {
            color: #8a70d6 !important;
            border-bottom: 3px solid #8a70d6 !important;
            background: rgba(138, 112, 214, 0.05) !important;
        }
        .table-hover tbody tr:hover {
            background-color: rgba(138, 112, 214, 0.02);
        }
    </style>

    <div class="container py-5">

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row align-items-center mb-5 g-3">
            <div class="col-12 col-md-auto">
                <h1 class="h3 mb-1">Inventario</h1>
                <p class="text-muted-custom mb-0">Gestiona los precintos</p>
            </div>
            <div class="col-12 col-md flex-grow-1">
                <form action="{{ route('control-riesgo.inventario') }}" method="GET" class="mx-md-4">
                    <div class="input-group shadow-sm" style="max-width: 500px;">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="q" class="form-control border-start-0" placeholder="Buscar precinto..." value="{{ $search }}">
                        @if($search)
                            <a href="{{ route('control-riesgo.inventario') }}" class="btn btn-outline-secondary"><i class="bi bi-x-lg"></i></a>
                        @endif
                        <button class="btn btn-primary px-4" type="submit">Buscar</button>
                    </div>
                </form>
            </div>
            <div class="col-12 col-md-auto">
                <button class="btn btn-primary w-100 shadow-sm" data-bs-toggle="modal" data-bs-target="#addPrecintoModal">
                    <i class="bi bi-plus-circle me-1"></i> Agregar Precinto
                </button>
            </div>
        </div>

        <div class="row g-4 mb-5">
            {{-- Disponibles --}}
            <div class="col-6 col-md-3">
                <div class="card card-custom p-3 border-start border-4 border-success h-100">
                    <div class="d-flex justify-content-between">
                        <i class="bi bi-check-circle-fill text-success fs-4"></i>
                        <span class="badge bg-success bg-opacity-10 text-success d-none d-sm-inline-block">Listos</span>
                    </div>
                    <div class="mt-3">
                        <p class="text-muted-custom mb-0 small text-uppercase fw-bold" style="font-size: 0.7rem;">Disponibles</p>
                        <div class="stat-value">{{ $disponibles->total() }}</div>
                        <small class="text-muted small d-block" style="font-size: 0.7rem;">Oficina central</small>
                    </div>
                </div>
            </div>

            {{-- Traslados (En Logística) --}}
            <div class="col-6 col-md-3">
                <div class="card card-custom p-3 border-start border-4 border-warning h-100">
                    <div class="d-flex justify-content-between">
                        <i class="bi bi-truck text-warning fs-4"></i>
                        <span class="badge bg-warning bg-opacity-10 text-warning d-none d-sm-inline-block">En Tránsito</span>
                    </div>
                    <div class="mt-3">
                        <p class="text-muted-custom mb-0 small text-uppercase fw-bold" style="font-size: 0.7rem;">Traslados</p>
                        <div class="stat-value">{{ $enLogistica->total() }}</div>
                        <small class="text-muted small d-block" style="font-size: 0.7rem;">Zona de cargue</small>
                    </div>
                </div>
            </div>

            {{-- Ingresados (Total Histórico) --}}
            <div class="col-6 col-md-3">
                <div class="card card-custom p-3 border-start border-4 border-primary h-100">
                    <div class="d-flex justify-content-between">
                        <i class="bi bi-box-seam text-primary fs-4"></i>
                        <span class="badge bg-primary bg-opacity-10 text-primary d-none d-sm-inline-block">Total</span>
                    </div>
                    <div class="mt-3">
                        <p class="text-muted-custom mb-0 small text-uppercase fw-bold" style="font-size: 0.7rem;">Ingresados</p>
                        <div class="stat-value">{{ $totalIngresados }}</div>
                        <small class="text-muted small d-block" style="font-size: 0.7rem;">Historial</small>
                    </div>
                </div>
            </div>

            {{-- Uso Mensual --}}
            <div class="col-6 col-md-3">
                <div class="card card-custom p-3 border-start border-4 border-danger h-100">
                    <div class="d-flex justify-content-between">
                        <i class="bi bi-calendar-check text-danger fs-4"></i>
                        <span class="badge bg-danger bg-opacity-10 text-danger d-none d-sm-inline-block">Mes Actual</span>
                    </div>
                    <div class="mt-3">
                        <p class="text-muted-custom mb-0 small text-uppercase fw-bold" style="font-size: 0.7rem;">Uso Mensual</p>
                        <div class="stat-value">{{ $usoMensual }}</div>
                        <small class="text-muted small d-block" style="font-size: 0.7rem;">Consumidos</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Section for Type Breakdown with Quick Transfer --}}
        <div class="mb-2">
            <h6 class="text-muted-custom small text-uppercase fw-bold mb-3">Disponibilidad por Tipo</h6>
            <div class="d-flex gap-2 mb-4 overflow-auto pb-2" style="scrollbar-width: thin;">
                @foreach($disponiblesPorTipo as $disponible)
                    <div class="card card-custom border-0 shadow-sm d-flex flex-row align-items-center p-2 px-3 bg-white" style="min-width: 180px;">
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center mb-1">
                                <span class="bullet bg-success d-inline-block me-1" style="width:8px; height:8px; border-radius:50%;"></span>
                                <span class="small fw-bold text-muted text-uppercase" style="font-size: 0.65rem;">{{ $disponible->tipo }}</span>
                            </div>
                            <div class="stat-value" style="font-size: 1.1rem;">{{ $disponible->total }} <small class="text-muted fw-normal" style="font-size: 0.7rem">disp.</small></div>
                        </div>
                        <button class="btn btn-sm btn-warning rounded-pill px-2 py-1 fw-bold ms-2" 
                                style="font-size: 0.7rem;"
                                onclick="openTransferModal('{{ $disponible->tipo }}', {{ $disponible->total }}, null)">
                            <i class="bi bi-truck"></i>
                        </button>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="card card-custom overflow-hidden">
            <div class="card-header bg-white p-0 border-bottom">
                <ul class="nav nav-tabs border-0" id="inventoryTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active px-4 py-3 fw-bold border-0 rounded-0" id="disponibles-tab"
                            data-bs-toggle="tab" data-bs-target="#disponibles" type="button" role="tab" onclick="setTab('disponibles')">
                            <i class="bi bi-check-circle me-2"></i>Disponibles ({{ $disponibles->total() }})
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link px-4 py-3 fw-bold border-0 rounded-0" id="logistica-tab" data-bs-toggle="tab"
                            data-bs-target="#logistica" type="button" role="tab" onclick="setTab('logistica')">
                            <i class="bi bi-truck me-2"></i>En Logística ({{ $enLogistica->total() }})
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link px-4 py-3 fw-bold border-0 rounded-0" id="usados-tab" data-bs-toggle="tab"
                            data-bs-target="#usados" type="button" role="tab" onclick="setTab('usados')">
                            <i class="bi bi-archive me-2"></i>Historial de Uso ({{ $usados->total() }})
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link px-4 py-3 fw-bold border-0 rounded-0" id="anulados-tab" data-bs-toggle="tab"
                            data-bs-target="#anulados" type="button" role="tab" onclick="setTab('anulados')">
                            <i class="bi bi-x-octagon me-2"></i>Anulados ({{ $anulados->total() }})
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link px-4 py-3 fw-bold border-0 rounded-0" id="todos-tab" data-bs-toggle="tab"
                            data-bs-target="#todos" type="button" role="tab" onclick="setTab('todos')">
                            <i class="bi bi-list-ul me-2"></i>Total Ingresos ({{ $todos->total() }})
                        </button>
                    </li>
                </ul>
            </div>
            <div class="card-body p-0">
                <div class="tab-content" id="inventoryTabsContent">
                    {{-- Disponibles --}}
                    <div class="tab-pane fade show active" id="disponibles" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4">Código</th>
                                        <th>Tipo</th>
                                        <th>Fecha Ingreso</th>
                                        <th class="text-end pe-4">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($disponibles as $precinto)
                                        <tr>
                                            <td class="ps-4 fw-bold text-primary">{{ $precinto->codigo }}</td>
                                            <td><span class="badge bg-secondary bg-opacity-10 text-secondary">{{ $precinto->tipo }}</span></td>
                                            <td>{{ $precinto->fecha_ingreso->format('d/m/Y H:i') }}</td>
                                            <td class="text-end pe-4">
                                                <button class="btn btn-sm btn-outline-warning" onclick="openTransferModal('{{ $precinto->tipo }}', 1, '{{ $precinto->codigo }}')">
                                                    <i class="bi bi-truck"></i> Trasladar
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="4" class="text-center py-5 text-muted">No hay precintos disponibles.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="p-3 border-top">
                            {{ $disponibles->appends(['q' => $search, 'tab' => 'disponibles'])->links() }}
                        </div>
                    </div>

                    {{-- En Logística --}}
                    <div class="tab-pane fade" id="logistica" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4">Código</th>
                                        <th>Tipo</th>
                                        <th>Fecha Traslado</th>
                                        <th>Logística ID</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($enLogistica as $precinto)
                                        <tr>
                                            <td class="ps-4 fw-bold">{{ $precinto->codigo }}</td>
                                            <td><span class="badge bg-info bg-opacity-10 text-info">{{ $precinto->tipo }}</span></td>
                                            <td>{{ $precinto->updated_at->format('d/m/Y H:i') }}</td>
                                            <td><span class="badge bg-light text-dark">#{{ $precinto->logistica_id }}</span></td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="4" class="text-center py-5 text-muted">No hay precintos en tránsito.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="p-3 border-top">
                            {{ $enLogistica->appends(['q' => $search, 'tab' => 'logistica'])->links() }}
                        </div>
                    </div>

                    {{-- Usados --}}
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
                                            <td><span class="badge bg-dark bg-opacity-10 text-dark">{{ $precinto->tipo }}</span></td>
                                            <td>{{ $precinto->usado_at ? $precinto->usado_at->format('d/m/Y H:i') : '-' }}</td>
                                            <td><span class="text-danger fw-bold">{{ $precinto->numero_contenedor ?? '-' }}</span></td>
                                            <td><a href="{{ route('control-riesgo.show', $precinto->form_response_id) }}" class="btn btn-sm btn-link">#{{ $precinto->form_response_id }}</a></td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="5" class="text-center py-5 text-muted">No hay registros de uso recientes.</td></tr>
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
                                        <th>Motivo RECHAZO</th>
                                        <th>Inspección</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($anulados as $precinto)
                                        <tr>
                                            <td class="ps-4 fw-bold text-danger">{{ $precinto->codigo }}</td>
                                            <td><span class="badge bg-danger bg-opacity-10 text-danger">{{ $precinto->tipo }}</span></td>
                                            <td>{{ $precinto->updated_at->format('d/m/Y H:i') }}</td>
                                            <td class="small">
                                                @if($precinto->formResponse)
                                                    <span class="text-muted">{{ Str::limit($precinto->formResponse->rejection_reason, 50) }}</span>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                @if($precinto->form_response_id)
                                                    <a href="{{ route('control-riesgo.show', $precinto->form_response_id) }}" class="btn btn-sm btn-link text-danger">#{{ $precinto->form_response_id }}</a>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="5" class="text-center py-5 text-muted">No hay precintos anulados.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="p-3 border-top">
                            {{ $anulados->appends(['q' => $search, 'tab' => 'anulados'])->links() }}
                        </div>
                    </div>

                    {{-- Todos los Ingresos --}}
                    <div class="tab-pane fade" id="todos" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4">Código</th>
                                        <th>Tipo</th>
                                        <th>Fecha Ingreso</th>
                                        <th>Estado Actual</th>
                                        <th>¿Consumido?</th>
                                        <th>Detalles</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($todos as $precinto)
                                        <tr>
                                            <td class="ps-4 fw-bold">{{ $precinto->codigo }}</td>
                                            <td><span class="badge bg-secondary bg-opacity-10 text-secondary">{{ $precinto->tipo }}</span></td>
                                            <td>{{ $precinto->created_at->format('d/m/Y H:i') }}</td>
                                            <td>
                                                @switch($precinto->estado)
                                                    @case('disponible')
                                                        <span class="badge bg-success">Disponible</span>
                                                        @break
                                                    @case('en_logistica')
                                                        <span class="badge bg-warning text-dark">En Logística</span>
                                                        @break
                                                    @case('usado')
                                                        <span class="badge bg-primary">Consumido / Usado</span>
                                                        @break
                                                    @case('anulado')
                                                        <span class="badge bg-danger">Anulado</span>
                                                        @break
                                                    @default
                                                        <span class="badge bg-secondary">{{ $precinto->estado }}</span>
                                                @endswitch
                                            </td>
                                            <td>
                                                @if($precinto->estado == 'usado')
                                                    <span class="text-success fw-bold"><i class="bi bi-check-circle-fill me-1"></i>SÍ</span>
                                                @elseif($precinto->estado == 'anulado')
                                                    <span class="text-danger fw-bold"><i class="bi bi-x-circle-fill me-1"></i>RECHAZADO</span>
                                                @else
                                                    <span class="text-muted"><i class="bi bi-circle me-1"></i>NO</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($precinto->form_response_id)
                                                    <a href="{{ route('control-riesgo.show', $precinto->form_response_id) }}" class="btn btn-sm btn-link">Ver Inspección</a>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="6" class="text-center py-5 text-muted">No hay registros de precintos.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="p-3 border-top">
                            {{ $todos->appends(['q' => $search, 'tab' => 'todos'])->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Modal -->
    <div class="modal fade" id="addPrecintoModal" tabindex="-1" aria-labelledby="addPrecintoModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content card-custom">
                <div class="modal-header border-bottom border-secondary">
                    <h5 class="modal-title" id="addPrecintoModalLabel">Agregar Precinto</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form action="{{ route('control-riesgo.inventario.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="codigos" class="form-label text-muted-custom">Códigos (uno por línea)</label>
                            <textarea class="form-control border-secondary" id="codigos" name="codigos" rows="5" required
                                placeholder="Ej:&#10;PR-001&#10;PR-002&#10;PR-003"></textarea>
                            <small class="text-muted">Ingrese cada código en una línea diferente. Se crearán registros
                                individuales para cada uno.</small>
                        </div>
                        <div class="mb-3">
                            <label for="tipo" class="form-label text-muted-custom">Tipo de Precinto</label>
                            <select class="form-select  border-secondary" id="tipo" name="tipo" required>
                                <option value="">Seleccione un tipo</option>
                                <option value="Botella">Botella</option>
                                <option value="Guaya">Guaya</option>
                                <option value="Satelital">Satelital</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer border-top border-secondary">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar Precintos</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Traslado -->
    <div class="modal fade" id="transferModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content card-custom">
                <div class="modal-header border-bottom border-secondary">
                    <h5 class="modal-title">Trasladar a Logística</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('control-riesgo.inventario.trasladar') }}" method="POST">
                    @csrf
                    <input type="hidden" name="tipo" id="transfer_tipo">
                    <input type="hidden" name="codigo" id="transfer_specific_codigo">
                    <div class="modal-body">
                        <div class="alert alert-info border-info border-opacity-25 bg-info bg-opacity-10 text-info">
                            <i class="bi bi-info-circle me-2"></i>
                            Va a trasladar precintos de tipo: <strong id="transfer_tipo_label"></strong>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted-custom">Cantidad a trasladar</label>
                            <input type="number" name="cantidad" id="transfer_cantidad" class="form-control border-secondary" 
                                   min="1" required>
                            <div class="form-text text-muted-custom">Máximo disponible: <span id="transfer_max"></span></div>
                        </div>
                    </div>
                    <div class="modal-footer border-top border-secondary">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-warning">Confirmar Traslado</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openTransferModal(tipo, max, codigo = null) {
            document.getElementById('transfer_tipo').value = tipo;
            document.getElementById('transfer_tipo_label').innerText = tipo + (codigo ? ' (' + codigo + ')' : '');
            
            document.getElementById('transfer_specific_codigo').value = codigo || '';
            const qtyInput = document.getElementById('transfer_cantidad');
            qtyInput.max = max;
            document.getElementById('transfer_max').innerText = max;
            qtyInput.value = 1;

            if (codigo) {
                qtyInput.value = 1;
                qtyInput.readOnly = true;
            } else {
                qtyInput.readOnly = false;
            }
            
            var modal = new bootstrap.Modal(document.getElementById('transferModal'));
            modal.show();
        }

        // Set tab in URL to preserve state on refresh/pagination
        function setTab(tabName) {
            const url = new URL(window.location);
            url.searchParams.set('tab', tabName);
            window.history.replaceState({}, '', url);
        }

        // Activate tab from URL on load
        document.addEventListener('DOMContentLoaded', function() {
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
@endsection