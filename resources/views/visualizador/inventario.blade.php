@extends('layouts.admin')

@section('content')

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

        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="card card-custom p-3">
                    <div class="d-flex justify-content-between">
                        <i class="bi bi-box-seam text-primary fs-4"></i>
                    </div>
                    <div class="mt-3">
                        <p class="text-muted-custom mb-0">Total Unidades</p>
                        <div class="stat-value">{{ $precintos->count() }} <small class="fs-6 fw-normal">unidades</small>
                        </div>
                    </div>
                </div>
            </div>

            @foreach($disponiblesPorTipo as $disponible)
                <div class="col-md-3">
                    <div class="card card-custom p-3 border-start border-4 border-warning">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="mt-1">
                                <p class="text-muted-custom mb-0 small text-uppercase fw-bold">{{ $disponible->tipo }}</p>
                                <div class="stat-value">{{ $disponible->total }} <small
                                        class="fs-6 fw-normal text-muted">disp.</small></div>
                            </div>
                            <button class="btn btn-warning btn-sm shadow-sm"
                                onclick="openTransferModal('{{ $disponible->tipo }}', {{ $disponible->total }})"
                                title="Trasladar a Logística">
                                <i class="bi bi-truck"></i>
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="card card-custom p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="mb-0">Listado de Precintos</h5>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th class="text-muted-custom small text-uppercase">ID</th>
                            <th class="text-muted-custom small text-uppercase">Código</th>
                            <th class="text-muted-custom small text-uppercase">Tipo</th>
                            <th class="text-muted-custom small text-uppercase">Fecha Ingreso</th>
                            <th class="text-muted-custom small text-uppercase">Estado</th>
                            <th class="text-muted-custom small text-uppercase">Logística ID</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($precintos as $precinto)
                            <tr>
                                <td class="fw-bold">{{ $precinto->id }}</td>
                                <td class="fw-bold">{{ $precinto->codigo }}</td>
                                <td class="text-muted-custom fw-bold">{{ $precinto->tipo }}</td>
                                <td class="text-muted-custom fw-bold">{{ $precinto->fecha_ingreso->format('d/m/Y H:i') }}</td>
                                <td>
                                    @if($precinto->estado === 'disponible')
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25">
                                            {{ ucfirst($precinto->estado) }}
                                        </span>
                                    @elseif($precinto->estado === 'en_logistica')
                                        <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25">
                                            En Logística
                                        </span>
                                    @else
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25">
                                            {{ ucfirst($precinto->estado) }}
                                        </span>
                                    @endif
                                </td>
                                <td class="text-muted-custom font-monospace">
                                    {{ $precinto->logistica_id ? '#' . $precinto->logistica_id : '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-5">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="bi bi-inbox fs-1 mb-2"></i>
                                        <p>No hay precintos registrados</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
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
        function openTransferModal(tipo, max) {
            document.getElementById('transfer_tipo').value = tipo;
            document.getElementById('transfer_tipo_label').innerText = tipo;
            document.getElementById('transfer_cantidad').max = max;
            document.getElementById('transfer_max').innerText = max;
            document.getElementById('transfer_cantidad').value = 1;
            
            var modal = new bootstrap.Modal(document.getElementById('transferModal'));
            modal.show();
        }
    </script>
@endsection