@extends('layouts.admin')

@section('title', 'Administrador - ContainerCheck')

@section('content')
    <div class="container mt-5">
        <h2 class="mb-4">Reportes de Formularios Enviados</h2>

        <div class="card p-4">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Usuario</th>
                        <th>Versión</th>
                        <th>Fecha</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($responses as $resp)
                        <tr>
                            <td>#{{ $resp->id }}</td>
                            <td>{{ $resp->user->name }}</td>
                            <td>{{ $resp->formVersion->version }}</td>
                            <td>{{ $resp->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <a href="{{ route('admin.reports.show', $resp->id) }}"
                                    class="btn btn-sm btn-outline-primary">Ver Detalle</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
