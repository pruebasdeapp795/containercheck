<!DOCTYPE html>
<html>

<head>
    <title>Inspección #{{ $inspection->id }}</title>
    <style>
        body {
            font-family: sans-serif;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .meta {
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
            font-weight: bold;
        }

        .value {
            border-bottom: 1px solid #ccc;
            padding-left: 5px;
        }

        .signatures {
            margin-top: 30px;
            display: table;
            width: 100%;
        }

        .sig-block {
            display: table-cell;
            width: 50%;
            text-align: center;
        }

        .sig-img {
            max-width: 200px;
            max-height: 100px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Reporte de Inspección</h1>
        <h3>ID: {{ $inspection->id }}</h3>
    </div>

    <div class="meta">
        <strong>Inspector:</strong> {{ $inspection->inspector->name }}<br>
        <strong>Estado:</strong> {{ ucfirst($inspection->status) }}<br>
        <strong>Fecha:</strong> {{ $inspection->created_at->format('Y-m-d H:i') }}
    </div>

    @foreach($phases as $phase)
        <div class="phase">
            <div class="phase-title">{{ $phase->name }}</div>

            @foreach($phase->fields->sortBy('order') as $field)
                @php
                    $val = $inspection->values->where('field_id', $field->id)->first();
                    $displayValue = $val ? $val->value : '-';
                @endphp
                <div class="field">
                    <span class="field-label">{{ $field->label }}:</span>
                    <span class="value">{{ $displayValue }}</span>
                </div>
            @endforeach
        </div>
    @endforeach

    <div class="signatures">
        <div class="sig-block">
            <h4>Inspector</h4>
            @php $inspSig = $inspection->signatures->where('role', 'inspector')->first(); @endphp
            @if($inspSig && $inspSig->image_path)
                <!-- Assuming image_path is base64 or public url. If base64, use data uri -->
                <img src="{{ $inspSig->image_path }}" class="sig-img" alt="Firma Inspector">
                <br>
                <small>{{ $inspSig->signed_at->format('Y-m-d H:i') }}</small>
            @else
                <p>Sin firma</p>
            @endif
        </div>
        <div class="sig-block">
            <h4>Participante</h4>
            @php $partSig = $inspection->signatures->where('role', 'participant')->first(); @endphp
            @if($partSig && $partSig->image_path)
                <img src="{{ $partSig->image_path }}" class="sig-img" alt="Firma Participante">
                <br>
                <small>{{ $partSig->signed_at->format('Y-m-d H:i') }}</small>
            @else
                <p>Sin firma</p>
            @endif
        </div>
    </div>
</body>

</html>