<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle de Inspección - ContainerCheck</title>
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

        .phase-title {
            color: #8a70d6;
            border-bottom: 2px solid #8a70d6;
            padding-bottom: 10px;
            margin-bottom: 25px;
            margin-top: 30px;
        }

        .field-label {
            font-weight: 600;
            color: #666;
        }

        .field-value {
            font-weight: 400;
            color: #333;
        }

        .signature-img {
            border: 1px solid #ddd;
            border-radius: 8px;
            max-width: 400px;
            background: white;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="{{ route('control-riesgo.index') }}">ContainerCheck</a>
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

    <div class="container mt-5 mb-5" style="max-width: 900px;">
        <div class="mb-4 d-flex justify-content-between align-items-center">
            <div>
                <h2>Inspección #{{ $response->id }}
                    @if($response->status == 'rejected')
                        <span class="badge bg-danger fs-6 vertical-align-middle">RECHAZADA</span>
                    @endif
                </h2>
                <p class="text-muted">{{ $response->formVersion->version }} - Registrada el
                    {{ $response->created_at->format('d/m/Y H:i') }}
                </p>
            </div>
            <a href="{{ route('control-riesgo.reportes') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
        </div>

        <div class="card p-4">
            @foreach($response->formVersion->phases as $phase)
                <h4 class="phase-title">{{ $phase->name }}</h4>
                <div class="row g-4">
                    @foreach($phase->fields as $field)
                        @php
                            $fieldResp = $response->fieldResponses->where('field_id', $field->id)->first();
                        @endphp
                        <div class="col-md-6">
                            <div class="field-label small text-uppercase">{{ $field->label }}</div>
                            <div class="field-value fs-5">
                                @if($field->type == 'photo')
                                    @if($fieldResp && $fieldResp->value)
                                        <div class="mt-2 text-center text-md-start">
                                            <img src="{{ asset('storage/' . $fieldResp->value) }}" class="img-fluid rounded shadow-sm"
                                                style="max-height: 250px;">
                                        </div>
                                    @else
                                        <span class="text-muted italic">Sin imagen</span>
                                    @endif
                                @else
                                    {{ $fieldResp->value ?? '---' }}
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach

            <h4 class="phase-title">Firma de Conformidad</h4>
            <div class="text-center p-3">
                <img src="{{ $response->signature }}" alt="Firma" class="signature-img img-fluid">
                <p class="mt-2 text-muted small">Firmado digitalmente el
                    {{ $response->signed_at->format('d/m/Y H:i:s') }}
                </p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>