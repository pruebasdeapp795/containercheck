<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso Personal - ContainerCheck</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <link href="{{ asset('css/portal.css') }}" rel="stylesheet">
</head>

<body>

    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand text-white fw-bold" href="{{ route('portal') }}">
                <img src="{{ asset('imagenes/logo_blanco.png') }}" width="180px" alt="Logo">
            </a>
        </div>
    </nav>

    <div class="main-container">
        <div class="login-card">
            <h2>Acceso</h2>
            <p class="login-subtitle">Ingrese sus credenciales para continuar</p>

            <form action="{{ route('login.personal') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="cedula">Cédula de Identidad</label>
                    <input type="text" name="cedula" id="cedula" placeholder="Ej: 12345678" required autofocus>

                    @error('cedula')
                        <div class="error-box">
                            <strong>Error:</strong> {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <input type="password" name="password" id="password" placeholder="••••••••" required>
                </div>

                <button type="submit" class="btn-primary-dark">Ingresar al Sistema</button>
            </form>

            <a href="{{ route('portal') }}" class="back-link">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                    class="bi bi-arrow-left me-1" viewBox="0 0 16 16">
                    <path fill-rule="evenodd"
                        d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z" />
                </svg>
                Volver al portal
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>