<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Revisión Monitoreo - #{{ $response->id }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-blue: #003366;
            --soft-gray: #f1f5f9;
            --border-color: #e2e8f0;
            --text-dark: #0f172a;
            --text-muted: #475569;
            --label-bg: #f8fafc;
        }

        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            background-color: #eef2f6;
            padding-bottom: 50px;
        }

        .report-custom-container {
            max-width: 307mm;
            margin: 20px auto;
            background: white;
            padding: 10mm;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        /* Estilos del reporte (copiados para consistencia visual) */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            border-bottom: 2px solid var(--primary-blue);
        }

        .header-title {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            color: var(--primary-blue);
            text-transform: uppercase;
        }

        .header-info-box {
            font-size: 10px;
            text-align: right;
            color: var(--text-muted);
            line-height: 1.2;
        }

        .phase-header {
            background-color: var(--primary-blue);
            color: #ffffff;
            font-weight: bold;
            text-transform: uppercase;
            padding: 5px 12px;
            margin: 15px 0 0 0;
            font-size: 11px;
            letter-spacing: 1px;
        }

        .data-grid {
            display: flex;
            flex-wrap: wrap;
            border: 1px solid var(--border-color);
        }

        .data-item {
            display: flex;
            width: 50%;
            border-bottom: 1px solid var(--border-color);
            border-right: 1px solid var(--border-color);
            font-size: 16px;
        }

        .data-item.full-width {
            width: 100%;
            border-right: none;
        }

        .data-label {
            width: 300px;
            font-weight: bold;
            background: var(--label-bg);
            padding: 5px 8px;
            color: var(--text-muted);
            display: flex;
            align-items: center;
        }

        .data-value {
            flex: 1;
            padding: 5px 8px;
            background: #fff;
            color: var(--text-dark);
            word-break: break-word;
        }

        .personal-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
            font-size: 16px;
            border: 1px solid var(--border-color);
        }

        .personal-table th {
            background: var(--soft-gray);
            color: var(--primary-blue);
            padding: 5px;
            text-align: left;
        }

        .personal-table td {
            padding: 5px;
            border-bottom: 1px solid var(--border-color);
        }

        .approval-section {
            background-color: #fff8e1;
            border: 2px solid #ffecb3;
            padding: 20px;
            margin-top: 30px;
            border-radius: 8px;
        }

        .signature-pad-container {
            border: 2px dashed #ccc;
            background: #fff;
            margin-bottom: 10px;
        }

        @media print {
            .no-print {
                display: none !important;
            }

            body {
                background: white;
            }

            .report-custom-container {
                box-shadow: none;
                margin: 0;
                padding: 0;
            }
        }
    </style>
</head>

<body>
    <div class="container-fluid no-print py-3 bg-white border-bottom shadow-sm fixed-top">
        <div class="d-flex justify-content-between align-items-center container">
            <a href="{{ route('monitoreo.index') }}" class="btn btn-outline-secondary">
                &larr; Volver al Panel
            </a>
            <h5 class="m-0 fw-bold">Revisión de Inspección #{{ $response->id }}</h5>
            <div>
                <span
                    class="badge {{ $response->status == 'completed' ? 'bg-success' : ($response->status == 'rejected' ? 'bg-danger' : 'bg-warning text-dark') }}">
                    {{ $response->status == 'completed' ? 'LIBERADA' : ($response->status == 'rejected' ? 'RECHAZADA' : 'PENDIENTE FIRMA') }}
                </span>
            </div>
        </div>
    </div>

    <div style="height: 80px;" class="no-print"></div>

    <div class="report-custom-container">
        {{-- Header --}}
        <table class="header-table">
            <tr>
                <td style="width: 25%;">
                    <img src="{{ asset('imagenes/tubosalogo.png') }}" width="200px" alt="ContainerCheck">
                </td>
                <td class="header-title">
                    Reporte de Inspección<br>
                    <span style="font-size: 10px; color: #666; font-weight: normal;">Versión:
                        {{ $response->formVersion->version }}</span>
                </td>
                <td style="width: 25%;" class="header-info-box">
                    <strong>FECHA:</strong> {{ $response->created_at->format('d/m/Y') }}<br>
                    <strong>HORA:</strong> {{ $response->created_at->format('H:i') }}<br>
                    <strong>DESPACHADOR:</strong> {{ $response->user->name }}<br>
                    <strong>EXPORTACIÓN</strong> 
                </td>
            </tr>
        </table>

        {{-- Content Loop --}}
        @foreach($response->formVersion->phases as $phase)
            <div class="phase-header">{{ $phase->name }}</div>

            @if(str_contains(strtoupper($phase->name), 'PERSONAL DEL CARGUE'))
                <table class="personal-table">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Cédula</th>
                            <th>Cargo</th>
                            <th>Chaleco</th>
                            <th>Firma</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($response->inspectionSignatures as $sig)
                            <tr>
                                <td>{{ $sig->user->name }}</td>
                                <td>{{ $sig->user->cedula }}</td>
                                <td>{{ $sig->role_in_inspection }}</td>
                                <td>{{ $sig->vest_number }}</td>
                                <td>
                                    @if($sig->signature)
                                        <img src="{{ $sig->signature }}" style="height: 20px;">
                                    @else
                                        <span class="text-muted">---</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

            @php
                // Filter out special fields
                $personalFieldIds = [57, 58, 59, 65];
                $fields = $phase->fields->whereNotIn('id', $personalFieldIds);
            @endphp

            @if($fields->count() > 0)
                <div class="data-grid">
                    @foreach($fields as $field)
                        @php
                            $val = $response->fieldResponses->where('field_id', $field->id)->first()?->value;
                            if (!$val || $field->type === 'photo')
                                continue;
                            $isFull = strlen($val) > 50 || $field->type === 'textarea';
                        @endphp
                        <div class="data-item {{ $isFull ? 'full-width' : '' }}">
                            <div class="data-label">{{ $field->label }}</div>
                            <div class="data-value">
                                {{ is_array(json_decode($val)) ? implode(', ', json_decode($val)) : $val }}
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        @endforeach

        {{-- QR Section for Photos --}}
        <div class="mt-4 p-3 border rounded bg-light text-center">
            <small class="text-muted text-uppercase fw-bold">Registro Fotográfico:</small><br>
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data={{ urlencode(route('reportes.gallery', $response->id)) }}"
                alt="QR" style="width: 80px; height: 80px;" class="mt-2">
            <br>
            <a href="{{ route('reportes.gallery', $response->id) }}" target="_blank"
                class="small text-decoration-none">Ver Galería &rarr;</a>
        </div>

        {{-- Firmas --}}
        <div class="mt-4 row">
            <div class="col-6 text-center">
                @if($response->signature)
                    <img src="{{ $response->signature }}" style="max-height: 60px; margin-bottom: 5px;"><br>
                @endif
                <div style="border-top: 1px solid #000; width: 80%; margin: 0 auto;"></div>
                <small class="fw-bold">DESPACHADOR</small><br>
                <small class="text-muted">
                    {{ $response->user->name ?? 'N/A' }}<br>
                    CC: {{ $response->user->cedula ?? 'N/A' }}
                </small>
            </div>
            <div class="col-6 text-center">
                @if($response->monitoreo_signature)
                    <img src="{{ $response->monitoreo_signature }}" style="max-height: 60px; margin-bottom: 5px;"><br>
                    <div style="border-top: 1px solid #000; width: 80%; margin: 0 auto;"></div>
                    <small class="fw-bold">MONITOREO</small><br>
                    <small class="text-muted">{{ $response->monitoreoUser->name ?? 'Firma Autorizada' }}</small><br>
                    <small class="text-muted">CC: {{ $response->monitoreoUser->cedula ?? 'N/A' }}</small>
                @else
                    <div style="height: 65px;"></div>
                    <div style="border-top: 1px solid #ccc; width: 80%; margin: 0 auto;"></div>
                    <small class="text-muted">Pendiente por Liberar</small>
                @endif
            </div>
        </div>

        {{-- Approval Section (Only if Pending) --}}
        @if($response->status === 'pending_monitoreo')
            <div class="approval-section no-print">
                <h4 class="fw-bold text-dark mb-3"><i class="bi bi-shield-check"></i> Liberación de Inspección</h4>
                <p class="text-muted small">
                    Al liberar, se aplicará su firma guardada y la inspección cambiará a estado <strong>COMPLETADO</strong>.
                </p>

                @if(!Auth::user()->saved_signature)
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        Debe <a href="{{ route('monitoreo.index') }}" class="alert-link">cargar su firma</a> antes de liberar
                        inspecciones.
                    </div>
                @else
                    <div class="mb-3 p-3 bg-light border rounded">
                        <small class="text-muted d-block mb-2">Vista previa de su firma:</small>
                        <img src="{{ Auth::user()->saved_signature }}"
                            style="max-height: 80px; border: 1px solid #ddd; padding: 5px; background: white;">
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <form action="{{ route('monitoreo.liberate', $response->id) }}" method="POST" id="approvalForm"
                                onsubmit="return confirm('¿Está seguro de que desea liberar esta inspección?')">
                                @csrf
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-success btn-lg fw-bold">
                                        <i class="bi bi-check-circle me-2"></i>LIBERAR INSPECCIÓN
                                    </button>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-6">
                            <div class="d-grid">
                                <button type="button" class="btn btn-danger btn-lg fw-bold" data-bs-toggle="modal"
                                    data-bs-target="#rejectModal">
                                    <i class="bi bi-x-circle me-2"></i>RECHAZAR INSPECCIÓN
                                </button>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        @endif
    </div>
    {{-- Modal Rechazo --}}
    <div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('monitoreo.reject', $response->id) }}" method="POST">
                    @csrf
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="rejectModalLabel">Motivo de Rechazo</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="rejection_reason" class="form-label fw-bold">Describa el motivo del
                                rechazo:</label>
                            <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="4"
                                required placeholder="Ej: Precinto no coincide con el registro..."></textarea>
                        </div>
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            Al rechazar la inspección, se notificará automáticamente a los responsables vía correo
                            electrónico.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger px-4 fw-bold">CONFIRMAR RECHAZO</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>