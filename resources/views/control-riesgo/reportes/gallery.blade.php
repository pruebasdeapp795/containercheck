<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Soporte Fotográfico - Inspección #{{ $response->id }}</title>
    <style>
        :root {
            --primary-blue: #003366;
            --bg-dark: #0f172a;
            --card-bg: #1e293b;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background-color: var(--bg-dark);
            color: var(--text-main);
            margin: 0;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            width: 100%;
            max-width: 800px;
        }

        .header h1 {
            font-size: 1.5rem;
            margin: 0;
            color: #fff;
        }

        .header p {
            color: var(--text-muted);
            font-size: 0.9rem;
            margin-top: 5px;
        }

        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
            width: 100%;
            max-width: 1000px;
        }

        .photo-card {
            background: var(--card-bg);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
        }

        .photo-card:hover {
            transform: translateY(-5px);
        }

        .photo-card img {
            width: 100%;
            height: 300px;
            object-fit: cover;
            cursor: pointer;
        }

        .photo-info {
            padding: 15px;
        }

        .photo-label {
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #38bdf8;
        }

        .photo-date {
            font-size: 1.25rem;
            color: var(--text-muted);
            margin-top: 5px;
        }

        @media (max-width: 640px) {
            .gallery-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <div class="header">
        <img src="{{ asset('imagenes/logo.png') }}" style="width: 120px; margin-bottom: 20px;" alt="Logo">
        <h1>Soporte Fotográfico de Inspección</h1>
        <p>ID: #{{ $response->id }} | Fecha: {{ \Carbon\Carbon::parse($response->created_at)->format('d/m/Y H:i') }}</p>
    </div>

    @if(count($photos) > 0)
        <div class="gallery-grid">
            @foreach($photos as $photo)
                <div class="photo-card">
                    <img src="{{ asset('storage/' . $photo['path']) }}" alt="{{ $photo['label'] }}"
                        onclick="window.open(this.src, '_blank')">
                    <div class="photo-info">
                        <div class="photo-label">{{ $photo['label'] }}</div>
                        @if ($photo['label'] == 'Foto de lado')
                            <div class="photo-date"># Contenedor: {{ $response->getContainerNumber() }}</div>
                        @endif
                        
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div style="text-align: center; padding: 50px;">
            <p style="color: var(--text-muted);">No se encontraron fotografías para esta inspección.</p>
        </div>
    @endif

    <footer style="margin-top: 50px; text-align: center; color: var(--text-muted); font-size: 0.8rem; padding: 20px;">
        &copy; {{ date('Y') }} Container Check - Control de Riesgos
    </footer>
</body>

</html>