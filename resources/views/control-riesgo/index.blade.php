<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Control de Riesgo - ContainerCheck</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

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

        .navbar-brand {
            font-weight: 800;
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            gap: 8px;
            color: #2d2d2d;
        }

        .brand-dot-grid {
            display: grid;
            grid-template-columns: repeat(3, 4px);
            gap: 3px;
        }

        .brand-dot-grid span {
            width: 4px;
            height: 4px;
            background-color: #8a70d6;
            border-radius: 50%;
        }

        .nav-link {
            color: #555 !important;
            font-weight: 500;
            margin: 0 10px;
            font-size: 0.95rem;
        }

        .nav-link.active {
            color: #8a70d6 !important;
        }

        .nav-link:hover {
            color: #8a70d6 !important;
        }

        .profile-section {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .notification-bell {
            font-size: 1.2rem;
            color: #333;
            cursor: pointer;
        }

        .avatar-circle {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background-image: url('https://via.placeholder.com/35');
            background-size: cover;
            border: 1px solid #ddd;
        }

        .logout-container {
            padding: 2rem;
        }

        @media (max-width: 991.98px) {
            .navbar {
                padding: 0.8rem 1rem;
            }

            .profile-section {
                margin-top: 1rem;
                padding-top: 1rem;
                border-top: 1px solid #eee;
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }

            .notification-bell {
                display: none;
            }

            .avatar-circle {
                display: none;
            }

            .profile-info-mobile {
                display: block !important;
                font-weight: 600;
                color: #2d2d2d;
                margin-bottom: 5px;
            }

            .dropdown-menu {
                display: block;
                position: static;
                margin: 0;
                padding: 0;
                border: none;
                background: transparent;
                box-shadow: none;
            }

            .dropdown-header {
                padding: 0;
                font-size: 1rem;
                color: #2d2d2d;
                font-weight: 600;
                margin-bottom: 0.5rem;
            }

            .dropdown-divider {
                display: none;
            }

            .dropdown-item {
                padding: 0.5rem 0;
                font-size: 0.95rem;
            }

            .btn-logout-mobile {
                width: 100%;
                text-align: left;
                padding: 0.75rem 0;
                border-radius: 0;
                color: #dc3545 !important;
                font-weight: 600;
                display: flex;
                align-items: center;
                gap: 10px;
                border: none;
                background: transparent;
            }
        }

        @media (min-width: 992px) {
            .profile-info-mobile {
                display: none !important;
            }
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                ContainerCheck
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navContent">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navContent">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="#">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="#">Inspecciones</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Reportes</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Formatos</a>
                    </li>
                </ul>

                <div class="profile-section ms-lg-4">
                    <i class="bi bi-bell-fill notification-bell"></i>

                    <div class="dropdown">
                        <div class="avatar-circle" id="profileMenu" data-bs-toggle="dropdown" aria-expanded="false"
                            style="cursor: pointer;">
                        </div>

                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-2"
                            aria-labelledby="profileMenu">
                            <li>
                                <h6 class="dropdown-header px-3 d-none d-lg-block">Usuario CC</h6>
                            </li>
                            <li class="d-lg-none">
                                <span class="profile-info-mobile">Usuario CC</span>
                            </li>
                            <li class="d-none d-lg-block">
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST" class="px-lg-2">
                                    @csrf
                                    <button type="submit"
                                        class="dropdown-item text-danger d-flex align-items-center gap-2 btn-logout-mobile">
                                        <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h2>Hola, Control Riesgo</h2>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>