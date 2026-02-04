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
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold">Bienvenido, {{ Auth::user()->name }}</h2>
                <p class="text-muted">Tienes <span class="badge bg-danger">{{ $pendingSignatures->count() }}</span>
                    documentos pendientes por firmar.</p>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row g-4">
            @forelse($pendingSignatures as $sig)
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-header bg-white py-3">
                            <h6 class="card-title fw-bold m-0 text-primary">Inspección #{{ $sig->form_response_id }}</h6>
                        </div>
                        <div class="card-body">
                            <p class="card-text small text-muted mb-1">Inspector</p>
                            <p class="fw-semibold">{{ $sig->formResponse->user->name }}</p>

                            <p class="card-text small text-muted mb-1">Fecha de Inicio</p>
                            <p class="fw-semibold">{{ $sig->formResponse->created_at->format('d/m/Y H:i A') }}</p>

                            <p class="card-text small text-muted mb-1">Tu Rol Asignado</p>
                            <span class="badge bg-secondary">{{ $sig->role_in_inspection }}</span>
                        </div>
                        <div class="card-footer bg-white border-top-0 pb-3">
                            <button class="btn btn-primary w-100"
                                onclick="openSignatureModal({{ $sig->id }}, 'Inspección #{{ $sig->form_response_id }}')">
                                <i class="bi bi-pen-fill me-2"></i>Firmar Documento
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-check-circle display-4 text-success mb-3"></i>
                        <h5>¡Estás al día!</h5>
                        <p>No tienes documentos pendientes por firmar.</p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Modal Firma -->
    <div class="modal fade" id="signatureModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="signForm" method="POST" action="">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold" id="modalTitle">Firmar Documento</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="text-muted small">Por favor firma en el recuadro de abajo para confirmar tu
                            participación.</p>
                        <div class="border rounded bg-light" style="touch-action: none;">
                            <canvas id="signature-pad" style="width: 100%; height: 200px; display: block;"></canvas>
                        </div>
                        <input type="hidden" name="signature_data" id="signatureData">
                        <div class="text-end mt-2">
                            <button type="button" class="btn btn-sm btn-link text-danger" id="clearPad">Borrar
                                Firma</button>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar Firma</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
    <script>
        let signaturePad;
        const form = document.getElementById('signForm');

        document.addEventListener('DOMContentLoaded', () => {
            const canvas = document.getElementById('signature-pad');
            signaturePad = new SignaturePad(canvas, { backgroundColor: 'rgb(248, 249, 250)' });

            // Resize canvas
            function resizeCanvas() {
                const ratio = Math.max(window.devicePixelRatio || 1, 1);
                canvas.width = canvas.offsetWidth * ratio;
                canvas.height = canvas.offsetHeight * ratio;
                canvas.getContext("2d").scale(ratio, ratio);
                signaturePad.clear();
            }
            window.addEventListener("resize", resizeCanvas);
            // Initial call slightly delayed to ensure modal render logic (though we handle it on show)
        });

        const modalEl = document.getElementById('signatureModal');
        modalEl.addEventListener('shown.bs.modal', function () {
            const canvas = document.getElementById('signature-pad');
            const ratio = Math.max(window.devicePixelRatio || 1, 1);
            canvas.width = canvas.offsetWidth * ratio;
            canvas.height = canvas.offsetHeight * ratio;
            canvas.getContext("2d").scale(ratio, ratio);
            signaturePad.clear();
        });

        function openSignatureModal(id, title) {
            document.getElementById('modalTitle').innerText = 'Firmando: ' + title;
            form.action = "/personal/sign/" + id;
            const bsModal = new bootstrap.Modal(modalEl);
            bsModal.show();
        }

        document.getElementById('clearPad').addEventListener('click', () => signaturePad.clear());

        form.addEventListener('submit', (e) => {
            if (signaturePad.isEmpty()) {
                e.preventDefault();
                alert('Por favor agrega tu firma.');
            } else {
                document.getElementById('signatureData').value = signaturePad.toDataURL();
            }
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>