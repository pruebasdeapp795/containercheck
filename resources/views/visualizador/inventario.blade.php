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
                        <div class="stat-value">{{ $precintos->sum('cantidad') }} <small
                                class="fs-6 fw-normal">unidades</small></div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card card-custom p-3">
                    <div class="d-flex justify-content-between">
                        <i class="bi bi-exclamation-triangle text-danger fs-4"></i>
                        <span class="badge badge-critical">Critico</span>
                    </div>
                    <div class="mt-3">
                        <p class="text-muted-custom mb-0">Alerta de Stock</p>
                        <div class="stat-value">0</div>
                    </div>
                </div>
            </div>
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
                            <th class="text-muted-custom small text-uppercase">Cantidad</th>
                            <th class="text-muted-custom small text-uppercase">Fecha Ingreso</th>
                            <th class="text-muted-custom small text-uppercase">Estado</th>
                            <th class="text-muted-custom small text-uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($precintos as $precinto)
                            <tr>
                                <td class="fw-bold">{{ $precinto->id }}</td>
                                <td class="fw-bold">{{ $precinto->codigo }}</td>
                                <td class="text-muted-custom fw-bold">{{ $precinto->tipo }}</td>
                                <td class="fw-bold">{{ $precinto->cantidad }}</td>
                                <td class="text-muted-custom fw-bold">{{ $precinto->fecha_ingreso->format('d/m/Y H:i') }}</td>
                                <td>
                                    <span class="badge bg-success bg-opacity-10 text-success">
                                        {{ ucfirst($precinto->estado) }}
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-outline-warning btn-sm" title="Trasladar a Logística">
                                        <i class="bi bi-truck"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-5">
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
                            <label for="codigo" class="form-label text-muted-custom">Código</label>
                            <input type="text" class="form-control  border-secondary" id="codigo"
                                name="codigo" required placeholder="Ej: PR-123456">
                        </div>
                        <div class="mb-3">
                            <label for="tipo" class="form-label text-muted-custom">Tipo</label>
                            <select class="form-select  border-secondary" id="tipo" name="tipo" required>
                                <option value="">Seleccione un tipo</option>
                                <option value="Botella">Botella</option>
                                <option value="Cable">Cable</option>
                                <option value="Plastico">Plástico</option>
                                <option value="Metálico">Metálico</option>
                                <option value="Otro">Otro</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="cantidad" class="form-label text-muted-custom">Cantidad</label>
                            <input type="number" class="form-control  border-secondary" id="cantidad"
                                name="cantidad" min="1" required placeholder="1">
                        </div>
                    </div>
                    <div class="modal-footer border-top border-secondary">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection