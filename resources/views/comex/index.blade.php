<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Contenedores Rechazados - ContainerCheck</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary-bg: #f5f7fa;
            --card-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            --header-color: #2d3436;
            --accent-red: #ff7675;
            --accent-blue: #0984e3;
            --soft-red: #ffeaa7;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--primary-bg);
            color: #2d3436;
        }

        .navbar {
            background-color: #ffffff;
            border-bottom: 1px solid #e1e4e8;
            padding: 1rem 2rem;
        }

        .dashboard-container {
            padding: 2rem;
            max-width: 1600px;
            margin: 0 auto;
        }

        .report-header {
            margin-bottom: 2rem;
        }

        .report-title {
            font-weight: 800;
            font-size: 1.75rem;
            color: var(--header-color);
            margin-bottom: 0.25rem;
        }

        .report-subtitle {
            font-weight: 500;
            color: #636e72;
            font-size: 1rem;
        }

        .card {
            border: none;
            border-radius: 16px;
            box-shadow: var(--card-shadow);
            background: #fff;
            height: 100%;
        }

        .card-header-custom {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid #f1f2f6;
            font-weight: 700;
            font-size: 1.1rem;
        }

        .stat-card {
            padding: 1.5rem;
        }

        .stat-value {
            font-size: 2.5rem;
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .stat-label {
            font-weight: 600;
            color: #636e72;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }

        .stat-desc {
            font-size: 0.9rem;
            color: #b2bec3;
            margin-top: 0.5rem;
        }

        .rejection-down {
            color: #d63031;
            font-size: 1.5rem;
        }

        .chart-container {
            padding: 1.5rem;
            position: relative;
            height: 300px;
        }

        .table-card {
            margin-top: 2rem;
        }

        .table thead th {
            background-color: #f8f9fa;
            border-bottom: 2px solid #edeff2;
            text-transform: uppercase;
            font-size: 0.75rem;
            font-weight: 700;
            color: #636e72;
            padding: 1rem;
        }

        .table tbody td {
            padding: 1rem;
            vertical-align: middle;
            font-size: 0.9rem;
        }

        .rejected-row {
            background-color: #fff5f5;
        }

        .badge-status {
            padding: 0.5em 1em;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.75rem;
            background-color: #ffeaa7;
            color: #d63031;
        }

        .btn-export {
            background-color: #27ae60;
            color: white;
            border: none;
            border-radius: 8px;
            padding: 0.6rem 1.25rem;
            font-weight: 600;
            transition: all 0.2s;
        }

        .btn-export:hover {
            background-color: #219150;
            color: white;
            transform: translateY(-1px);
        }

        .donut-legend {
            font-size: 0.85rem;
            list-style: none;
            padding: 0;
            margin-top: 1rem;
        }

        .donut-legend li {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 4px;
        }

        .legend-color {
            width: 12px;
            height: 12px;
            border-radius: 3px;
        }
    </style>
</head>

<body>
    <nav class="navbar d-flex justify-content-between align-items-center">
        <a href="#">
            <img src="{{ asset('imagenes/containerchecklogov.png') }}" width="140px" alt="ContainerCheck">
        </a>
        <div class="d-flex align-items-center gap-3">
            <span class="badge bg-secondary">COMEX Access</span>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-danger">Cerrar Sesión</button>
            </form>
        </div>
    </nav>

    <div class="dashboard-container">
        <div class="report-header d-flex justify-content-between align-items-end">
            <div>
                <h1 class="report-title">Reporte de Contenedores Rechazados</h1>
                <p class="report-subtitle">{{ now()->translatedFormat('F Y') }} - Q{{ ceil(now()->month / 3) }}
                    {{ now()->year }}
                </p>
            </div>
            <div>
                <a href="{{ route('comex.export.csv') }}" class="btn btn-export shadow-sm">
                    <i class="bi bi-file-earmark-excel me-2"></i> Exportar Excel (CSV)
                </a>
            </div>
        </div>

        <div class="row g-4">
            <!-- Executive Summary -->
            <div class="col-lg-5">
                <div class="card">
                    <div class="card-header-custom">Resumen Ejecutivo</div>
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="stat-card">
                                    <div class="stat-label">Contenedores Rechazados</div>
                                    <div class="stat-value">
                                        {{ $totalRejected }}
                                        <i class="bi bi-arrow-down-short rejection-down"></i>
                                    </div>
                                    <div class="stat-desc">Tendencia vs Mes anterior</div>
                                </div>
                            </div>
                            <div class="col-md-6 text-md-end">
                                <div class="stat-card">
                                    <div class="stat-label">Tasa de Rechazo</div>
                                    <div class="stat-value justify-content-md-end">{{ $rejectionRate }}%</div>
                                    <div class="stat-desc">Del total de envíos</div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-2 g-4">
                            <div class="col-6">
                                <div class="stat-label text-center mb-2">Causas Principales</div>
                                <canvas id="causasChart" style="max-height: 180px;"></canvas>
                            </div>
                            <div class="col-6">
                                <div class="stat-label text-center mb-2">Clientes Críticos</div>
                                <canvas id="autoridadChart" style="max-height: 180px;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Column -->
            <div class="col-lg-7">
                <div class="card">
                    <div class="card-header-custom">Rechazos por Cliente</div>
                    <div class="chart-container">
                        <canvas id="origenChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="card table-card">
            <div class="card-header-custom">Detalle de Contenedores Inspeccionados</div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Folio</th>
                            <th>Contenedor</th>
                            <th>Fecha</th>
                            <th>Cliente</th>
                            <th>Inspector</th>
                            <th>Motivo de Rechazo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rejectedInspections as $ins)
                            <tr class="rejected-row">
                                <td><span class="fw-bold">#{{ $ins->id }}</span></td>
                                <td>{{ $ins->getContainerNumber() }}</td>
                                <td>{{ $ins->updated_at->format('d/m/Y H:i') }}</td>
                                <td>{{ $ins->getFieldValue('Cliente') ?? 'N/A' }}</td>
                                <td>
                                    {{ $ins->user->name ?? 'N/A' }}<br>
                                    <small class="text-muted">Rechazado por:
                                        {{ $ins->monitoreoUser->name ?? 'incumplimiento de la norma' }}</small>
                                </td>
                                <td>
                                    <span class="text-danger fw-500">{{ $ins->getRejectionReason() }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">No hay registros de rechazos.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        // Chart Config
        const accentBlue = '#0984e3';
        const accentRed = '#ff7675';
        const softRed = '#fab1a0';
        const colors = ['#0984e3', '#ff7675', '#27ae60', '#f1c40f', '#8e44ad', '#2c3e50'];

        // Causas Chart (Donut)
        new Chart(document.getElementById('causasChart'), {
            type: 'doughnut',
            data: {
                labels: {!! json_encode(array_keys($reasons)) !!},
                datasets: [{
                    data: {!! json_encode(array_values($reasons)) !!},
                    backgroundColor: ['#0984e3', '#ff7675', '#fab1a0', '#dfe6e9'],
                    borderWidth: 0
                }]
            },
            options: {
                cutout: '70%',
                plugins: { legend: { display: false } }
            }
        });

        // Top Clients Chart (Donut)
        new Chart(document.getElementById('autoridadChart'), {
            type: 'doughnut',
            data: {
                labels: {!! json_encode(array_keys($topClients)) !!},
                datasets: [{
                    data: {!! json_encode(array_values($topClients)) !!},
                    backgroundColor: colors,
                    borderWidth: 0
                }]
            },
            options: {
                cutout: '70%',
                plugins: { legend: { display: false } }
            }
        });

        // Origen/Destino Chart (Bar)
        new Chart(document.getElementById('origenChart'), {
            type: 'bar',
            data: {
                labels: {!! json_encode(array_keys($topClients)) !!},
                datasets: [{
                    label: 'Contenedores Rechazados',
                    data: {!! json_encode(array_values($topClients)) !!},
                    backgroundColor: '#0984e3',
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { display: false },
                        ticks: {
                            // Esta función obliga a que solo se muestren enteros
                            stepSize: 1,
                            callback: function (value) {
                                if (value % 1 === 0) {
                                    return value;
                                }
                            }
                        }
                    },
                    x: { grid: { display: false } }
                }
            }
        });

        // Tendencia Chart (Line)
        new Chart(document.getElementById('tendenciaChart'), {
            type: 'line',
            data: {
                labels: {!! json_encode($monthlyTrend->pluck('month')) !!},
                datasets: [{
                    label: 'Rechazos',
                    data: {!! json_encode($monthlyTrend->pluck('count')) !!},
                    borderColor: '#ff7675',
                    backgroundColor: 'rgba(255, 118, 117, 0.1)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 3,
                    pointRadius: 4,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#ff7675',
                    pointBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { color: '#f1f2f6' } },
                    x: { grid: { display: false } }
                }
            }
        });
    </script>
</body>

</html>