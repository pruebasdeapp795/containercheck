<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle de Formulario #{{ $response->id }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
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

        .phase-section {
            border-left: 4px solid #8a70d6;
            padding-left: 20px;
            margin-bottom: 30px;
        }

        .signature-img {
            border: 1px solid #ddd;
            border-radius: 8px;
            max-width: 400px;
            display: block;
            background: #fff;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="{{ route('admin.index') }}">ContainerCheck</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="{{ route('admin.reports.index') }}">Volver a
                            Reportes</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5 mb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2>Detalle de Formulario #{{ $response->id }}</h2>
                <p class="text-muted">Enviado por {{ $response->user->name }} el
                    {{ $response->created_at->format('d/m/Y H:i') }}</p>
            </div>
            <button class="btn btn-outline-secondary" onclick="window.print()">Imprimir</button>
        </div>

        @foreach($response->formVersion->phases as $phase)
            <div class="card p-4">
                <div class="phase-section">
                    <h4>{{ $phase->name }}</h4>
                </div>
                <div class="row">
                    @foreach($phase->fields as $field)
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold text-muted small d-block">{{ $field->label }}</label>
                            <div class="p-2 bg-light rounded shadow-sm">
                                @php
                                    $fieldResp = $response->fieldResponses->where('field_id', $field->id)->first();
                                @endphp
                                @if($fieldResp)
                                    @if($field->type == 'photo')
                                        <a href="{{ asset('storage/' . $fieldResp->value) }}" target="_blank">
                                            <img src="{{ asset('storage/' . $fieldResp->value) }}" class="img-fluid rounded"
                                                style="max-height: 200px;">
                                        </a>
                                    @else
                                        {{ $fieldResp->value }}
                                    @endif
                                @else
                                    <span class="text-muted italic">Sin respuesta</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach

        <div class="card p-4">
            <h4 class="mb-3">Firma</h4>
            @if($response->signature)
                <img src="{{ $response->signature }}" class="signature-img">
                <p class="mt-2 text-muted small">Firmado el:  {{ $response->signed_at ? \Carbon\Carbon::parse($response->signed_at)->format('H:i') : '' }}</p>
            @else
                <p class="text-danger">No se encontró firma.</p>
            @endif
        </div>
    </div>
</body>

</html>