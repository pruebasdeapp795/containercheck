<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal de Acceso - ContainerCheck</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <link href="{{ asset('css/portal.css') }}" rel="stylesheet">
</head>

<body>

    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand text-white fw-bold" href="#">
                <img src="{{ asset('imagenes/logo_blanco.png') }}" width="200px" alt="ContainerCheck">
            </a>
            <button class="navbar-toggler border-white" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon" style="filter: invert(1);"></span>
            </button>
        </div>
    </nav>

    <div class="container my-auto">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <div class="hero-section">
                    <h2 class="display-title">Bienvenido.<br>Entra.<br>Gestiona.</h2>
                    <p class="hero-p">
                        Acceda a la plataforma de Inspeccion de Contenedores. Eficiencia y control en un solo lugar.
                    </p>
                </div>
            </div>
            <div class="col-lg-4 offset-lg-1">
                <div class="d-grid gap-3">
                    <a href="{{ route('login.admin') }}" class="access-card">Ingresar como Administrador</a>
                    <a href="{{ route('login.control-riesgo') }}" class="access-card">Panel de Control Riesgo
                        (Despachos/Monitoreo)</a>
                    <a href="{{ route('login.personal') }}" class="access-card">Acceso Personal</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>