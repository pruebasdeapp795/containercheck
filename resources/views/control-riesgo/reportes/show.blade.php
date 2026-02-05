<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte Inspección - {{ $response->id }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            font-size: 12px;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        @media print {
            .print-btn {
                display: none;
            }
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: -1px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
            vertical-align: middle;
        }

        .header-bg {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: center;
        }

        .logo-cell {
            width: 20%;
            text-align: center;
        }

        .title-cell {
            width: 60%;
            text-align: center;
            font-size: 1.2em;
            font-weight: bold;
        }

        .info-cell {
            width: 20%;
            text-align: center;
            font-weight: bold;
        }

        .label {
            background-color: #e0e0e0;
            font-weight: bold;
            width: 15%;
            text-align: center;
        }

        .col-no {
            width: 35px;
            background-color: #e0e0e0;
            font-weight: bold;
            text-align: center;
        }

        .input-box {
            width: 35%;
        }

        .section-header {
            background-color: #e0e0e0;
            font-weight: bold;
            text-transform: uppercase;
            text-align: center;
        }

        .field-label {
            font-weight: bold;
            background-color: #f9f9f9;
            width: 20%;
        }

        .print-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 10px 20px;
            background: #8a70d6;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }

        @media print {
            .print-btn {
                display: none;
            }

            body {
                margin: 0;
            }
        }
    </style>
</head>

<body>
    @php
        // Helper to find value by label substring
        $getValue = function ($labelPart) use ($response) {
            foreach ($response->formVersion->phases as $phase) {
                foreach ($phase->fields as $field) {
                    // Check if label contains the search part (case insensitive)
                    if (str_contains(strtoupper($field->label), strtoupper($labelPart))) {
                        $resp = $response->fieldResponses->where('field_id', $field->id)->first();
                        return $resp ? $resp->value : '';
                    }
                }
            }
            return '';
        };

        // Helper specifically for Phase 13 (Personal del Cargue)
        $getPersonalSignatures = function () use ($response) {
            return $response->inspectionSignatures;
        };
    @endphp

    <button onclick="window.print()" class="print-btn">Imprimir Reporte</button>

    <table>
        <tbody>
            <tr>
                <td class="logo-cell" rowspan="3">
                    <img src="https://tubosa.com/wp-content/uploads/2025/02/tubosa-header-logo.png" width="150"
                        alt="TUBOSA">
                </td>
                <td class="title-cell" rowspan="3">REPORTE INSPECCIÓN DEL CONTENEDOR</td>
                <td class="info-cell">FCR16</td>
            </tr>
            <tr>
                <td class="info-cell">Actualización N°2</td>
            </tr>
            <tr>
                <td class="info-cell">29 de Octubre 2025</td>
            </tr>
        </tbody>
    </table>

    <table>
        <tbody>
            <tr>
                <td class="label">Fecha:</td>
                <td class="input-box">{{ $getValue('Fecha') }}</td>
                <td class="label">Inicio Inspección:</td>
                <td class="input-box">{{ $getValue('Inicio') }}</td> <!-- Asumiendo que hay campo Hora Inicio -->
            </tr>
            <tr>
                <td class="label">No Contenedor:</td>
                <td class="input-box">{{ $getValue('Numero de contenedor') }}</td>
                <td class="label">Fin Inspección:</td>
                <td class="input-box">
                    {{ $response->signed_at ? \Carbon\Carbon::parse($response->signed_at)->format('H:i') : '' }}</td>
            </tr>
            <tr>
                <td class="label">Tipo de operación</td>
                <td class="input-box" colspan="3">{{ $getValue('Operación') }}</td>
            </tr>
        </tbody>
    </table>

    <table>
        <thead>
            <tr>
                <th class="col-no">NO</th>
                <th colspan="4" class="section-header">OBSERVACIONES SOBRE EL CONDUCTOR</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="col-no">1</td>
                <td class="col-no" colspan="4">A U D I T O R I A C O N D U C T O R</td>
            </tr>

            <tr>
                <td class="col-no" rowspan="2">1.1</td>
                <td class="field-label" style="width: 25%;">NOMBRE DEL CONDUCTOR:</td>
                <td colspan="3">{{ $getValue('Nombre del conductor') }}</td>
            </tr>
            <tr>
                <td class="field-label">CÉDULA:</td>
                <td style="width: 25%;">{{ $getValue('C.C') }}</td>
                <td class="field-label" style="width: 10%;">DE:</td>
                <td>{{ $getValue('De') }}</td> <!-- Verificar si existe campo 'De' en cedula -->
            </tr>

            <tr>
                <td class="col-no">1.2</td>
                <td class="field-label">Seguridad social y salud: ARL</td>
                <td>{{ $getValue('ARL') }}</td>
                <td class="field-label">EPS:</td>
                <td>{{ $getValue('EPS') }}</td>
            </tr>

            <tr>
                <td class="col-no">1.3</td>
                <td class="field-label">Describa el estado anímico del conductor:</td>
                <td colspan="3">{{ $getValue('estado anímico') }}</td>
            </tr>

            <tr>
                <td class="col-no">1.4</td>
                <td class="field-label">Tiene Licencia de Conducción? (PASE)</td>
                <td>{{ $getValue('Licencia') }}</td>
                <td class="field-label">Tiene tarjeta de propiedad del vehículo?</td>
                <td>{{ $getValue('tarjeta de propiedad') }}</td>
            </tr>

            <tr>
                <td class="col-no">1.5</td>
                <td class="field-label">Tiene seguro obligatorio del vehículo vigente?:</td>
                <td>{{ $getValue('seguro obligatorio') }}</td>
                <td class="field-label">Tiene TECNICOMECANICA vigente?:</td>
                <td>{{ $getValue('TECNICOMECANICA') }}</td>
            </tr>
        </tbody>

        <thead>
            <tr>
                <th class="col-no">NO</th>
                <th colspan="4" class="section-header">OBSERVACIONES SOBRE LA UNIDAD DE TRANSPORTE</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="col-no">2</td>
                <td class="col-no" colspan="4">AUDITORIA CABEZOTE - TRAILER</td>
            </tr>
            <tr>
                <td class="col-no">2.1</td>
                <td class="field-label" style="width: 35%;">Llantas en buen estado?*:</td>
                <td colspan="3">{{ $getValue('Llantas en buen estado') }}</td>
            </tr>
            <tr>
                <td class="col-no">2.2</td>
                <td class="field-label" style="width: 25%;">Las luces funcionan correctamente?*:</td>
                <td colspan="3">{{ $getValue('luces funcionan') }}</td>
            </tr>
            <tr>
                <td class="col-no">2.3</td>
                <td class="field-label" style="width: 25%;">Posee llanta de repuesto?</td>
                <td colspan="3">{{ $getValue('llanta de repuesto') }}</td>
            </tr>
        </tbody>

        <thead>
            <tr>
                <th class="col-no">NO</th>
                <th colspan="4" class="section-header">OBSERVACIONES SOBRE LA UNIDAD DE CARGA</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="col-no">3</td>
                <td class="col-no" colspan="4">IDENTIFICACIÓN</td>
            </tr>
            <tr>
                <td class="col-no">3.1</td>
                <td class="field-label" style="width: 25%;">Numero del contenedor es diferente en uno o mas de los 5
                    lados visibles *</td>
                <td colspan="3">{{ $getValue('Numero del contenedor es diferente') }}</td>
            </tr>
            <tr>
                <td class="col-no">3.2</td>
                <td class="field-label" style="width: 25%;">Numero del precinto de llegada contenedor vacío coincide con
                    el de salida del patio contenedores?? Numero: </td>
                <td colspan="3">{{ $getValue('Numero del precinto de llegada') }}</td>
            </tr>

            <tr>
                <td class="col-no">4</td>
                <td class="col-no" colspan="4">PUERTAS INTERIOR Y EXTERIOR</td>
            </tr>
            <!-- Seccion 4 campos -->
            <tr>
                <td class="col-no">4.1</td>
                <td class="field-label" style="width: 25%;">Adhesivo o pegante nuevo en uniones de las laminas (No
                    reportadas en comodato e
                    inspección transporte.) </td>
                <td colspan="3">{{ $getValue('Adhesivo o pegante nuevo en uniones de las laminas') }}</td>
            </tr>
            <tr>
                <td class="col-no">4.2</td>
                <td class="field-label" style="width: 25%;">Marcas o quemaduras recientes de soldadura (No reportadas en
                    comodato e
                    inspección transportista)</td>
                <td colspan="3">{{ $getValue('Marcas o quemaduras recientes de soldadura (No reportadas') }}</td>
            </tr>
            <tr>
                <td class="col-no">4.3</td>
                <td class="field-label" style="width: 25%;">Pintura nueva en partes o parches (No reportadas en comodato
                    e inspección
                    transportista)</td>
                <td colspan="3">{{ $getValue('Pintura nueva en partes') }}</td>
            </tr>
            <tr>
                <td class="col-no">4.4</td>
                <td class="field-label" style="width: 25%;">Ondulaciones internas y externas desiguales en tamaño y
                    altura *</td>
                <td colspan="3">{{ $getValue('Ondulaciones internas') }}</td>
            </tr>
            <tr>
                <td class="col-no">4.5</td>
                <td class="field-label" style="width: 25%;">Canales superiores e inferiores internos con tapas *</td>
                <td colspan="3">{{ $getValue('Canales superiores') }}</td>
            </tr>
            <tr>
                <td class="col-no">4.6</td>
                <td class="field-label" style="width: 25%;">Vigas y travesaños con sonido metálico disparejo (Diferente
                    en algún punto )*
                </td>
                <td colspan="3">{{ $getValue('Vigas y travesaños') }}</td>
            </tr>
            <tr>
                <td class="col-no">4.7</td>
                <td class="field-label" style="width: 25%;">Remaches y tuercas de los seguros de las manijas ocultos y
                    soldados en parte
                    interna *</td>
                <td colspan="3">{{ $getValue('Remaches y tuercas') }}</td>
            </tr>
            <tr>
                <td class="col-no">4.8</td>
                <td class="field-label" style="width: 25%;">Áreas aledañas a remaches o bisagras con muestra de golpes,
                    pinturas o forcejeo
                </td>
                <td colspan="3">{{ $getValue('Áreas aledañas') }}</td>
            </tr>
            <tr>
                <td class="col-no">4.9</td>
                <td class="field-label" style="width: 25%;">Olores a pintura, soldadura, madera quemada, pegante,
                    materiales de relleno,
                    grasa, etc.</td>
                <td colspan="3">{{ $getValue('Olores a pintura') }}</td>
            </tr>
            <tr>
                <td class="col-no">4.10</td>
                <td class="field-label" style="width: 25%;">El estado de mecanismos de cierre del contenedor, incluyendo
                    bisagras, manijas, guía o barra de cierre, Cerrojo es</td>
                <td colspan="3">{{ $getValue('El estado de mecanismos') }}</td>
            </tr>

            <tr>
                <td class="col-no">5</td>
                <td class="col-no" colspan="4">PISO</td>
            </tr>
            <tr>
                <td class="col-no">5.1</td>
                <td class="field-label" style="width: 25%;">Esta desnivelado*</td>
                <td colspan="3">{{ $getValue('Esta desnivelado') }}</td>
            </tr>
            <tr>
                <td class="col-no">5.2</td>
                <td class="field-label" style="width: 25%;">Se encuentra por encima de nivel de las vigas inferiores *
                </td>
                <td colspan="3">{{ $getValue('Se encuentra por encima') }}</td>
            </tr>
            <tr>
                <td class="col-no">5.3</td>
                <td class="field-label" style="width: 25%;">Reparaciones nuevas con malos acabados (No reportadas en
                    comodato e inspección
                    transportista)</td>
                <td colspan="3">{{ $getValue('Reparaciones nuevas') }}</td>
            </tr>

            <tr>
                <td class="col-no">6</td>
                <td class="col-no" colspan="4">TECHO INTERIOR Y EXTERIOR</td>
            </tr>
            <tr>
                <td class="col-no">6.1</td>
                <td class="field-label" style="width: 25%;">Los soportes (vigas superiores) se encuentran ocultos *</td>
                <td colspan="3">{{ $getValue('Los soportes') }}</td>
            </tr>
            <tr>
                <td class="col-no">6.2</td>
                <td class="field-label" style="width: 25%;">Orificios de ventilación sellados/ocultos *</td>
                <td colspan="3">{{ $getValue('Orificios de ventilación') }}</td>
            </tr>
            <tr>
                <td class="col-no">6.3</td>
                <td class="field-label" style="width: 25%;">Techos desnivelado *</td>
                <td colspan="3">{{ $getValue('Techos desnivelado') }}</td>
            </tr>
            <tr>
                <td class="col-no">6.4</td>
                <td class="field-label" style="width: 25%;">Marcas o quemaduras recientes de soldadura en el techo (No
                    reportadas en
                    comodato e inspección transp)</td>
                <td colspan="3">{{ $getValue('Marcas o quemaduras recientes de soldadura en el techo') }}</td>
            </tr>
            <tr>
                <td class="col-no">6.5</td>
                <td class="field-label" style="width: 25%;">Pintura nueva en partes o parches en el techo (No reportadas
                    en comodato e
                    inspección transportista)</td>
                <td colspan="3">
                    {{ $getValue('Pintura nueva en partes o parches (No reportadas en comodato e inspección transportista)') }}
                </td>
            </tr>

            <tr>
                <td class="col-no">7</td>
                <td class="col-no" colspan="4">CONTENEDOR 40" High Cube</td>
            </tr>
            <tr>
                <td class="col-no">7.1</td>
                <td class="field-label" style="width: 25%;">Largo exterior: 12,19 m</td>
                <td colspan="3">{{ $getValue('Largo exterior') }}</td>
            </tr>
            <tr>
                <td class="col-no">7.2</td>
                <td class="field-label" style="width: 25%;">Ancho exterior: 2,44 m</td>
                <td colspan="3">{{ $getValue('Ancho exterior') }}</td>
            </tr>
            <tr>
                <td class="col-no">7.3</td>
                <td class="field-label" style="width: 25%;">Alto exterior: 2,89 m</td>
                <td colspan="3">{{ $getValue('Alto exterior') }}</td>
            </tr>
            <tr>
                <td class="col-no">7.4</td>
                <td class="field-label" style="width: 25%;">Largo interior: 12,03 m</td>
                <td colspan="3">{{ $getValue('Largo interior') }}</td>
            </tr>
            <tr>
                <td class="col-no">7.5</td>
                <td class="field-label" style="width: 25%;">Ancho interior: 2,35 m</td>
                <td colspan="3">{{ $getValue('Ancho interior') }}</td>
            </tr>
            <tr>
                <td class="col-no">7.6</td>
                <td class="field-label" style="width: 25%;">Alto interior: 2,70 m</td>
                <td colspan="3">{{ $getValue('Alto interior') }}</td>
            </tr>

            <tr>
                <td class="col-no">8</td>
                <td class="col-no" colspan="4">COSTADOS DERECHOS E IZQUIERDO Y FRONTAL INTERNOS Y EXTERNOS</td>
            </tr>
            <tr>
                <td class="col-no">8.1</td>
                <td class="field-label" style="width: 25%;">Adhesivo o pegante nuevo en uniones de laminas</td>
                <td colspan="3">{{ $getValue('Adhesivo o pegante nuevo en uniones de laminas') }}</td>
            </tr>
            <tr>
                <td class="col-no">8.2</td>
                <td class="field-label" style="width: 25%;">Marcas o quemaduras recientes de soldadura</td>
                <td colspan="3">{{ $getValue('Marcas o quemaduras recientes de soldadura') }}</td>
            </tr>
            <tr>
                <td class="col-no">8.3</td>
                <td class="field-label" style="width: 25%;">Existen dos o mas colores de pintura</td>
                <td colspan="3">{{ $getValue('Existen dos o mas colores') }}</td>
            </tr>
            <tr>
                <td class="col-no">8.4</td>
                <td class="field-label" style="width: 25%;">Ondulaciones laterales con sonido metalicos disparejos *
                </td>
                <td colspan="3">{{ $getValue('Ondulaciones laterales') }}</td>
            </tr>
            <tr>
                <td class="col-no">8.5</td>
                <td class="field-label" style="width: 25%;">La altura entre piso, costados y techo se encuentran fuera
                    de estandares *</td>
                <td colspan="3">{{ $getValue('La altura entre piso') }}</td>
            </tr>
        </tbody>
    </table>

    <table>
        <tr>
            <td style="width: 50%;"><strong>Transportadora:</strong> {{ $getValue('Transportadora') }}</td>
            <td style="width: 50%;"><strong>Cliente:</strong> {{ $getValue('Cliente') }}</td>
        </tr>
        <tr>
            <td colspan="2"><strong>Número de los precintos de salida:</strong>
                {{ $getValue('Numero de los precintos de salida') }}</td>
        </tr>
    </table>

    <!-- Tabla Firmas Personal -->
    <table>
        <thead>
            <tr class="section-header">
                <th colspan="4">Personal que participan en el cargue:</th>
            </tr>
            <tr style="text-align: center; background-color: #f2f2f2;">
                <th style="width: 40%;">NOMBRE COMPLETO</th>
                <th style="width: 25%;">CARGO</th>
                <th style="width: 20%;">FIRMA</th>
                <th style="width: 15%;"># CHALECO</th>
            </tr>
        </thead>
        <tbody>
            @php $signatures = $getPersonalSignatures(); @endphp
            @if($signatures->count() > 0)
                @foreach($signatures as $sig)
                    <tr>
                        <td style="text-align: center;">{{ $sig->user->name }}</td>
                        <td style="text-align: center;">{{ $sig->role_in_inspection }}</td>
                        <td style="text-align: center;">
                            @if($sig->signature)
                                <img src="{{ $sig->signature }}" style="max-height: 40px;">
                            @else
                                Pendiente
                            @endif
                        </td>
                        <td style="text-align: center;">{{ $sig->vest_number }}</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="4" style="text-align: center;">No hay personal registrado</td>
                </tr>
            @endif
        </tbody>
    </table>

    <!-- Firmas Finales -->
    <table>
        <tbody>
            <tr>
                <td colspan="2" class="field-label" style="background-color: #fff; border-bottom: none;">
                    <strong>Inspeccionado por:</strong>
                </td>
            </tr>
            <tr>
                <td style="width: 50%; text-align: center; border-top: none; padding: 20px;">
                    <div style="height: 50px; width: 80%; margin: 0 auto;">
                        @if($response->signature)
                            <img src="{{ $response->signature }}" style="max-height: 50px;">
                        @endif
                    </div>
                    <div style="border-top: 1px solid #000; width: 80%; margin: 0 auto;"></div>

                    <div style="font-size: 10px; margin-top: 5px;">Responsable del despacho ( Firma )</div>


                    <div style="margin: 15px auto 0 auto; width: 80%; font-weight: bold;">
                        {{ $response->user->cedula ?? $response->user->id }}
                    </div>
                    <div style="border-top: 1px solid #000; width: 80%; margin: 0 auto;"></div>
                    <div style="font-size: 10px; margin-top: 5px;">Número de Cédula</div>
                </td>

                <td style="width: 50%; text-align: center; border-top: none; padding: 20px;">
                    <!-- Firma Control Riesgo (Inspector) -->
                    <div style="height: 50px; width: 80%; margin: 0 auto;">
                        @if($response->signature)
                            <img src="{{ $response->signature }}" style="max-height: 50px;">
                        @endif
                    </div>
                    <div style="border-top: 1px solid #000; width: 80%; margin: 0 auto;"></div>
                    <div style="font-size: 10px; margin-top: 5px;">Verificación documental Control Riesgos (Firma)</div>

                    <div style="margin: 15px auto 0 auto; width: 80%; font-weight: bold;">
                        {{ $response->user->cedula ?? $response->user->id }}
                    </div>
                    <div style="border-top: 1px solid #000; width: 80%; margin: 0 auto;"></div>
                    <div style="font-size: 10px; margin-top: 5px;">Número de Cédula</div>
                </td>
            </tr>
        </tbody>
    </table>

</body>

</html>