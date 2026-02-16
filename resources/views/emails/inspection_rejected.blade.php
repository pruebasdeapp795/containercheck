<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Inspección Rechazada</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 20px auto;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            overflow: hidden;
        }

        .header {
            background-color: #e53e3e;
            color: white;
            padding: 20px;
            text-align: center;
        }

        .content {
            padding: 20px;
        }

        .footer {
            background-color: #f7fafc;
            padding: 15px;
            text-align: center;
            font-size: 12px;
            color: #718096;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        .info-table td {
            padding: 10px;
            border-bottom: 1px solid #edf2f7;
        }

        .info-table td.label {
            font-weight: bold;
            color: #4a5568;
            width: 40%;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #3182ce;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }

        .rejection-box {
            background-color: #fff5f5;
            border-left: 4px solid #e53e3e;
            padding: 15px;
            margin-top: 10px;
            color: #c53030;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 14px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>ALERTA DE RECHAZO</h1>
        </div>
        <div class="content">
            <p>Se ha registrado una inspección con estado <strong>RECHAZADO</strong> en el sistema ContainerCheck.</p>

            <table class="info-table">
                <tr>
                    <td class="label">ID de Inspección:</td>
                    <td>#{{ $response->id }}</td>
                </tr>
                <tr>
                    <td class="label">Numero del contenedor:</td>
                    <td>{{ $response->getContainerNumber() }}</td>
                </tr>
                <tr>
                    <td class="label">Motivo de rechazo:</td>
                    <td>
                        <div class="rejection-box">
                            {{ $response->getRejectionReason() }}
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="label">Inspector:</td>
                    <td>{{ $response->user->name }}</td>
                </tr>
                <tr>
                    <td class="label">Fecha y Hora:</td>
                    <td>{{ $response->signed_at->format('d/m/Y H:i:s') }}</td>
                </tr>
            </table>
        </div>
        <div class="footer">
            Este es un correo automático generado por ContainerCheck. Por favor no responda a este mensaje.
        </div>
    </div>
</body>

</html>