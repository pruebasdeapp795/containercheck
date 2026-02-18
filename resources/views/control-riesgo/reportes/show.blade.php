<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte Inspección - {{ $response->id }}</title>
    <style>
        :root {
            --primary-blue: #003366;
            --soft-gray: #f1f5f9;
            --border-color: #e2e8f0;
            --text-dark: #0f172a;
            --text-muted: #475569;
            --label-bg: #f8fafc;
        }

        @page {
            size: A4;
            margin: 1cm;
        }

        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            font-size: 9px;
            line-height: 1.3;
            color: var(--text-dark);
            margin: 0;
            padding: 0;
            background-color: #fff;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        /* Utilidades */
        .no-print {
            text-align: right;
            margin-bottom: 15px;
        }

        .btn-print {
            padding: 8px 18px;
            background: var(--primary-blue);
            color: #fff;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            transition: opacity 0.2s;
        }

        @media print {
            .no-print {
                display: none;
            }

            body {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .phase-header {
                background-color: var(--primary-blue) !important;
                color: #ffffff !important;
            }

            .data-label {
                background-color: var(--label-bg) !important;
            }
        }

        /* Encabezado Estilo Moderno */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            border-bottom: 2px solid var(--primary-blue);
        }

        .header-table td {
            border: none;
            padding: 10px 5px;
            vertical-align: middle;
        }

        .header-logo {
            width: 120px;
        }

        .header-title {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            color: var(--primary-blue);
            text-transform: uppercase;
        }

        .header-info-box {
            font-size: 8px;
            text-align: right;
            color: var(--text-muted);
            line-height: 1.1;
        }

        /* Secciones (Phases) */
        .phase-header {
            background-color: var(--primary-blue);
            color: #ffffff;
            font-weight: bold;
            text-transform: uppercase;
            padding: 5px 12px;
            margin: 12px 0 0 0;
            font-size: 10px;
            letter-spacing: 1px;
            border-radius: 4px 4px 0 0;
        }

        /* Grid de Datos Estilo Moderno */
        .data-grid {
            display: flex;
            flex-wrap: wrap;
            border-top: 1px solid var(--border-color);
            border-left: 1px solid var(--border-color);
            border-radius: 4px;
            overflow: hidden;
        }

        .data-item {
            display: flex;
            width: 50%;
            border-bottom: 1px solid var(--border-color);
            border-right: 1px solid var(--border-color);
            box-sizing: border-box;
            min-height: 24px;
        }

        .data-item.full-width {
            width: 100%;
            border-right: none;
        }

        .data-item:nth-child(2n) {
            border-right: none;
        }

        .data-item:last-child {
            border-right: none;
        }

        .data-label {
            width: 140px;
            font-weight: bold;
            background: var(--label-bg);
            border-right: 1px solid var(--border-color);
            padding: 5px 8px;
            color: var(--text-muted);
            font-size: 8px;
            display: flex;
            align-items: center;
        }

        .data-value {
            flex: 1;
            padding: 5px 8px;
            background: #fff;
            color: var(--text-dark);
            font-weight: 500;
            word-break: break-word;
            display: flex;
            align-items: center;
        }

        /* Tabla de Personal */
        .personal-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
            border-radius: 4px;
            overflow: hidden;
            border: 1px solid var(--border-color);
        }

        .personal-table th {
            background: var(--soft-gray);
            color: var(--primary-blue);
            font-size: 9px;
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }

        .personal-table td {
            padding: 4px 8px;
            border-bottom: 1px solid var(--border-color);
        }

        /* Fotos */
        /* Sección QR */
        .qr-section {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 10px;
            border: 1px dashed var(--primary-blue);
            border-radius: 8px;
            margin: 10px 0;
            background-color: #f8fafc;
        }

        .qr-container {
            text-align: center;
            padding-right: 15px;
            border-right: 1px solid var(--border-color);
        }

        .qr-img {
            width: 80px;
            height: 80px;
        }

        .qr-text {
            padding-left: 15px;
            flex: 1;
        }

        .qr-title {
            font-size: 10px;
            font-weight: bold;
            color: var(--primary-blue);
            margin-bottom: 3px;
        }

        .qr-desc {
            font-size: 8px;
            color: var(--text-muted);
            line-height: 1.2;
        }
    </style>
</head>

<body>
    <div class="no-print">
        <button class="btn-print" onclick="window.print()">Descargar Reporte PDF</button>
    </div>

    @if($response->status === 'pending_monitoreo' || $response->status === 'rejected')
        <style>
            .watermark {
                position: fixed;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%) rotate(-45deg);
                font-size: 80px;
                color: rgba(255, 0, 0, 0.2);
                z-index: 9999;
                pointer-events: none;
                font-weight: bold;
                border: 5px solid rgba(255, 0, 0, 0.2);
                padding: 20px;
                text-transform: uppercase;
                white-space: nowrap;
            }
        </style>
        @if($response->status === 'pending_monitoreo')
            <div class="watermark">PENDIENTE LIBERACIÓN</div>
        @else
            <div class="watermark">REPORTE RECHAZADO</div>
        @endif
    @endif

    @php 
        $startTimeField = $response->formVersion->phases->flatMap->fields->where('label', 'Inicio de Inspección:')->first();
        $endTimeField = $response->formVersion->phases->flatMap->fields->where('label', 'Hora de Terminación Inspección')->first();
        $horaInicio = $response->fieldResponses->where('field_id', $startTimeField?->id)->first()?->value ?? '---';
        $horaFin = $response->fieldResponses->where('field_id', $endTimeField?->id)->first()?->value ?? '---';
        
        $photos = []; 
    @endphp

    {{-- Header --}}
    <table class="header-table">
        <tr>
            <td style="width: 25%;">
                <img src="{{ asset('imagenes/logo.png') }}" class="header-logo" alt="Logo">
            </td>
            <td class="header-title">
                Reporte de Inspección de Contenedor<br>
                <span style="font-size: 10px; color: #666; font-weight: normal;">ID: #{{ $response->id }} | Versión:
                    {{ $response->formVersion->version }}</span>
            </td>
            <td style="width: 25%;" class="header-info-box">
                <strong>CÓDIGO:</strong> FCR16<br>
                <strong>REVISIÓN:</strong> 03<br>
                <strong>FECHA:</strong> {{ \Carbon\Carbon::parse($response->created_at)->format('d/m/Y') }}<br>
                <strong>EXPORTACIÓN</strong> 
                
            </td>
        </tr>
    </table>

    @foreach($response->formVersion->phases as $phase)
        @php
            $isPersonal = str_contains(strtoupper($phase->name), 'PERSONAL DEL CARGUE');
            $phaseFields = $phase->fields;
            $responses = $response->fieldResponses;
        @endphp

        <div class="phase-header">{{ $phase->name }}</div>

        @if($isPersonal)
            <table class="personal-table">
                <thead>
                    <tr>
                        <th style="width: 30%;">Nombre</th>
                        <th style="width: 20%;">Cédula</th>
                        <th style="width: 20%;">Cargo</th>
                        <th style="width: 10%;">Chaleco</th>
                        <th style="width: 20%;">Firma</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($response->inspectionSignatures as $sig)
                        <tr>
                            <td>{{ $sig->user->name }}</td>
                            <td>{{ $sig->user->cedula }}</td>
                            <td>{{ $sig->role_in_inspection }}</td>
                            <td style="text-align: center;">{{ $sig->vest_number }}</td>
                            <td style="text-align: center;">
                                @if($sig->signature)
                                    <img src="{{ $sig->signature }}" style="max-height: 30px;">
                                @else
                                    <span style="color:#ccc">---</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        {{-- Mostrar campos adicionales de la fase (incluyendo el procesamiento de fotos para el QR) --}}
        @php
            $personalFieldIds = [57, 58, 59, 65];
            $fieldsToShow = $isPersonal
                ? $phaseFields->whereNotIn('id', $personalFieldIds)
                : $phaseFields;
        @endphp

        @if($fieldsToShow->count() > 0)
            <div class="data-grid" @if($isPersonal) style="margin-top: 5px; border-top: none;" @endif>
                @foreach($fieldsToShow as $field)
                    @php
                        $fieldResponse = $responses->where('field_id', $field->id)->first();
                        $value = $fieldResponse ? $fieldResponse->value : '';

                        // Si el campo es una foto, simplemente lo saltamos en el grid 
                        // (el QR lo detectará automáticamente de las respuestas)
                        if ($field->type === 'photo' || !$value) {
                            continue;
                        }

                        $isFullWidth = strlen($value) > 60 || $field->type === 'textarea';
                    @endphp

                    <div class="data-item {{ $isFullWidth ? 'full-width' : '' }}">
                        <div class="data-label">{{ strtoupper($field->label) }}</div>
                        <div class="data-value">
                            @if($field->type === 'date' && $value)
                                <strong>{{ \Carbon\Carbon::parse($value)->format('d/m/Y') }}</strong>
                            @elseif($field->type === 'time' && $value)
                                <strong>{{ \Carbon\Carbon::parse($value)->format('H:i') }}</strong>
                            @else
                                <strong>{{ is_array(json_decode($value)) ? implode(', ', json_decode($value)) : $value }}</strong>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    @endforeach

    @php
        $hasPhotos = false;
        foreach ($response->fieldResponses as $fr) {
            if ($fr->field->type === 'photo' && $fr->value) {
                $hasPhotos = true;
                break;
            }
        }
    @endphp

    @if($hasPhotos)
        <div class="phase-header">Soporte Fotográfico Digital</div>
        <div class="qr-section">
            <div class="qr-container">
                @php
                    $galleryUrl = route('reportes.gallery', $response->id);
                    $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" . urlencode($galleryUrl);
                @endphp
                <img src="{{ $qrUrl }}" class="qr-img" alt="QR Gallery">
            </div>
            <div class="qr-text">
                <div class="qr-title">ESCANEÉ PARA VER REGISTRO FOTOGRÁFICO</div>
                <div class="qr-desc">
                    Por motivos de optimización y claridad en el reporte impreso, las fotografías de esta inspección se
                    encuentran almacenadas en nuestro servidor seguro. <br>
                    <strong>Escanee el código QR</strong> o ingrese a: <br>
                    <span style="color: var(--primary-blue); font-size: 8px;">{{ $galleryUrl }}</span>
                </div>
            </div>
        </div>
    @endif

    <div class="signatures-area">
        <style>
            .signatures-area {
                margin-top: 15px;
            }

            .sig-table {
                width: 100%;
                border-collapse: collapse;
            }

            .sig-block {
                width: 50%;
                text-align: center;
                padding: 10px 20px;
            }

            .sig-img {
                max-height: 50px;
                max-width: 150px;
                margin-bottom: 5px;
            }

            .sig-line {
                border-top: 1.5px solid var(--text-dark);
                width: 80%;
                margin: 0 auto 8px;
            }

            .sig-name {
                font-weight: bold;
                font-size: 9px;
                text-transform: uppercase;
            }

            .sig-meta {
                color: var(--text-muted);
                font-size: 8px;
            }
        </style>
        <table class="sig-table">
            <tr>
                <td class="sig-block">
                    @if($response->signature)
                        <img src="{{ $response->signature }}" class="sig-img">
                    @else
                        <div style="height: 50px;"></div>
                    @endif
                    <div class="sig-line"></div>
                    <div class="sig-name">Despachador</div>
                    <div class="sig-meta">Responsable del despacho<br>CC: {{ $response->user->cedula ?? 'N/A' }}</div>
                </td>
                <td class="sig-block">
                    @if($response->monitoreo_signature)
                        <img src="{{ $response->monitoreo_signature }}" class="sig-img">
                        <div class="sig-line"></div>
                        <div class="sig-name">Verificación Monitoreo</div>
                        <div class="sig-meta">
                            {{ $response->monitoreoUser->name ?? 'Autorizado' }}<br>
                            Fecha: {{ $response->monitoreo_signed_at ? \Carbon\Carbon::parse($response->monitoreo_signed_at)->format('d/m/Y') : '' }}
                        </div>
                    @else
                        <div style="height: 50px;"></div>
                        <div class="sig-line"></div>
                        <div class="sig-name">Verificación</div>
                        <div class="sig-meta">Control Riesgos / Seguridad<br>Pendiente</div>
                    @endif
                </td>
            </tr>
        </table>
    </div>
</body>

</html>