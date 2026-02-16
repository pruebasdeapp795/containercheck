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

        <div class="d-flex justify-content-between align-items-center mb-5">
            <div>
                <h1 class="h3 mb-1">Inventario</h1>
                <p class="text-muted-custom">Gestiona los precintos</p>
            </div>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPrecintoModal">
                <i class="bi bi-plus-circle"></i> Agregar Precinto
            </button>
        </div>

        <div class="row g-4 mb-5">
            {{-- Disponibles --}}
            <div class="col-md-3">
                <div class="card card-custom p-3 border-start border-4 border-success h-100">
                    <div class="d-flex justify-content-between">
                        <i class="bi bi-check-circle-fill text-success fs-4"></i>
                        <span class="badge bg-success bg-opacity-10 text-success">Listos</span>
                    </div>
                    <div class="mt-3">
                        <p class="text-muted-custom mb-0 small text-uppercase fw-bold">Disponibles</p>
                        <div class="stat-value">{{ $disponibles->count() }}</div>
                        <small class="text-muted">En oficina central</small>
                    </div>
                </div>
            </div>

            {{-- Traslados (En Logística) --}}
            <div class="col-md-3">
                <div class="card card-custom p-3 border-start border-4 border-warning h-100">
                    <div class="d-flex justify-content-between">
                        <i class="bi bi-truck text-warning fs-4"></i>
                        <span class="badge bg-warning bg-opacity-10 text-warning">En Tránsito</span>
                    </div>
                    <div class="mt-3">
                        <p class="text-muted-custom mb-0 small text-uppercase fw-bold">Traslados</p>
                        <div class="stat-value">{{ $enLogistica->count() }}</div>
                        <small class="text-muted">En zona de cargue</small>
                    </div>
                </div>
            </div>

            {{-- Ingresados (Total Histórico) --}}
            <div class="col-md-3">
                <div class="card card-custom p-3 border-start border-4 border-primary h-100">
                    <div class="d-flex justify-content-between">
                        <i class="bi bi-box-seam text-primary fs-4"></i>
                        <span class="badge bg-primary bg-opacity-10 text-primary">Total</span>
                    </div>
                    <div class="mt-3">
                        <p class="text-muted-custom mb-0 small text-uppercase fw-bold">Ingresados</p>
                        <div class="stat-value">{{ $totalIngresados }}</div>
                        <small class="text-muted">Total histórico</small>
                    </div>
                </div>
            </div>

            {{-- Uso Mensual --}}
            <div class="col-md-3">
                <div class="card card-custom p-3 border-start border-4 border-danger h-100">
                    <div class="d-flex justify-content-between">
                        <i class="bi bi-calendar-check text-danger fs-4"></i>
                        <span class="badge bg-danger bg-opacity-10 text-danger">Mes Actual</span>
                    </div>
                    <div class="mt-3">
                        <p class="text-muted-custom mb-0 small text-uppercase fw-bold">Uso Mensual</p>
                        <div class="stat-value">{{ $usoMensual }}</div>
                        <small class="text-muted">Consumidos este mes</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Section for Type Breakdown (Optional but useful) --}}
        <div class="d-flex gap-2 mb-4 overflow-auto pb-2">
            @foreach($disponiblesPorTipo as $disponible)
                <div class="badge bg-white text-dark border p-2 px-3 shadow-sm rounded-pill d-flex align-items-center">
                    <span class="bullet bg-success me-2" style="width:8px; height:8px; border-radius:50%;"></span>
                    {{ $disponible->tipo }}: <strong class="ms-1">{{ $disponible->total }}</strong>
                </div>
            @endforeach
        </div>

        <div class="card card-custom overflow-hidden">
            <div class="card-header bg-white p-0 border-bottom">
                <ul class="nav nav-tabs border-0" id="inventoryTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active px-4 py-3 fw-bold border-0 rounded-0" id="disponibles-tab"
                            data-bs-toggle="tab" data-bs-target="#disponibles" type="button" role="tab">
                            <i class="bi bi-check-circle me-2"></i>Disponibles ({{ $disponibles->count() }})
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link px-4 py-3 fw-bold border-0 rounded-0" id="logistica-tab" data-bs-toggle="tab"
                            data-bs-target="#logistica" type="button" role="tab">
                            <i class="bi bi-truck me-2"></i>En Logística ({{ $enLogistica->count() }})
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link px-4 py-3 fw-bold border-0 rounded-0" id="usados-tab" data-bs-toggle="tab"
                            data-bs-target="#usados" type="button" role="tab">
                            <i class="bi bi-archive me-2"></i>Historial de Uso ({{ $usados->count() }})
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
                                            <td><a href="{{ route('visualizador.show', $precinto->form_response_id) }}" class="btn btn-sm btn-link">#{{ $precinto->form_response_id }}</a></td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="5" class="text-center py-5 text-muted">No hay registros de uso recientes.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
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
                <form action="{{ route('visualizador.inventario.store') }}" method="POST">
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
                <form action="{{ route('visualizador.inventario.trasladar') }}" method="POST">
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
                // Add hidden field for specific code if needed, but currently the controller expects quantity.
                // If the user wants to transfer a SPECIFIC seal, the current controller might need an update.
                // But for now, quantity=1 of that type is what's implemented.
            } else {
                qtyInput.readOnly = false;
            }
            
            var modal = new bootstrap.Modal(document.getElementById('transferModal'));
            modal.show();
        }
    </script>
@endsection