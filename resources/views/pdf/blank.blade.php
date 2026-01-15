<!DOCTYPE html>
<html>

<head>
    <title>Formato de Inspección</title>
    <style>
        body {
            font-family: sans-serif;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .phase {
            margin-bottom: 20px;
            border: 1px solid #ccc;
            padding: 10px;
        }

        .phase-title {
            background-color: #eee;
            padding: 5px;
            font-weight: bold;
        }

        .field {
            margin: 10px 0;
        }

        .field-label {
            display: inline-block;
            width: 200px;
            font-weight: bold;
        }

        .field-line {
            border-bottom: 1px solid #000;
            display: inline-block;
            width: 300px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Formato de Inspección</h1>
        <p>Pre-operacional de Contenedores</p>
    </div>

    @foreach($phases as $phase)
        <div class="phase">
            <div class="phase-title">{{ $phase->name }}</div>
            <div class="phase-desc">{{ $phase->description }}</div>

            @foreach($phase->fields->sortBy('order') as $field)
                <div class="field">
                    <span class="field-label">{{ $field->label }}:</span>
                    <span class="field-line">&nbsp;</span>
                </div>
            @endforeach
        </div>
    @endforeach

    <div class="phase">
        <div class="phase-title">Firmas</div>
        <div class="field" style="margin-top: 50px;">
            <span class="field-label">Firma Inspector:</span>
            <span class="field-line">__________________________</span>
        </div>
        <div class="field" style="margin-top: 50px;">
            <span class="field-label">Firma Tham (Participante):</span>
            <span class="field-line">__________________________</span>
        </div>
    </div>
</body>

</html>