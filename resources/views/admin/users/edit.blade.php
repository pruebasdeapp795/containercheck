@extends('layouts.admin')

@section('title', 'Editar Usuario - Admin')

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="mb-4">
                    <a href="{{ route('admin.users.index') }}"
                        class="btn btn-link link-dark p-0 text-decoration-none small fw-bold">
                        <i class="bi bi-arrow-left me-1"></i> Volver al listado
                    </a>
                    <h2 class="fw-bold mt-2 mb-1">Editar Usuario</h2>
                    <p class="text-muted">Actualiza la información del usuario <strong>{{ $user->name }}</strong>.</p>
                </div>

                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4 p-md-5">
                        <form action="{{ route('admin.users.update', $user) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row g-4">
                                <!-- Basic Info Section -->
                                <div class="col-12">
                                    <h6 class="text-uppercase text-muted small fw-bold mb-3 border-bottom pb-2">Información
                                        Básica</h6>
                                </div>

                                <div class="col-md-12">
                                    <label for="name" class="form-label fw-semibold">Nombre Completo</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                        name="name" value="{{ old('name', $user->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="cedula" class="form-label fw-semibold">Cédula / ID</label>
                                    <input type="text" class="form-control @error('cedula') is-invalid @enderror"
                                        id="cedula" name="cedula" value="{{ old('cedula', $user->cedula) }}" required>
                                    @error('cedula')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="email" class="form-label fw-semibold">Correo Electrónico</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                                        name="email" value="{{ old('email', $user->email) }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Account Access Section -->
                                <div class="col-12 mt-5">
                                    <h6 class="text-uppercase text-muted small fw-bold mb-3 border-bottom pb-2">Acceso al
                                        Sistema</h6>
                                </div>

                                <div class="col-md-6">
                                    <label for="usuario" class="form-label fw-semibold">Nombre de Usuario (Login)</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0 text-muted"><i
                                                class="bi bi-at"></i></span>
                                        <input type="text"
                                            class="form-control border-start-0 @error('usuario') is-invalid @enderror"
                                            id="usuario" name="usuario" value="{{ old('usuario', $user->usuario) }}"
                                            required>
                                    </div>
                                    @error('usuario')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="role" class="form-label fw-semibold">Rol del Usuario</label>
                                    <select class="form-select @error('role') is-invalid @enderror" id="role" name="role"
                                        required>
                                        @foreach($roles as $role)
                                            <option value="{{ $role }}" {{ old('role', $user->role) == $role ? 'selected' : '' }}>
                                                {{ ucfirst(str_replace('_', ' ', $role)) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('role')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 mt-4">
                                    <div class="alert alert-info border-0 shadow-sm rounded-3">
                                        <i class="bi bi-info-circle-fill me-2"></i> Deja los campos de contraseña en blanco
                                        si no deseas cambiarla.
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label for="password" class="form-label fw-semibold">Nueva Contraseña</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0 text-muted"><i
                                                class="bi bi-key"></i></span>
                                        <input type="password"
                                            class="form-control border-start-0 @error('password') is-invalid @enderror"
                                            id="password" name="password" placeholder="••••••••">
                                    </div>
                                    @error('password')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="password_confirmation" class="form-label fw-semibold">Confirmar Nueva
                                        Contraseña</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0 text-muted"><i
                                                class="bi bi-shield-check"></i></span>
                                        <input type="password" class="form-control border-start-0"
                                            id="password_confirmation" name="password_confirmation" placeholder="••••••••">
                                    </div>
                                </div>

                                <div class="col-12 mt-5">
                                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                        <a href="{{ route('admin.users.index') }}"
                                            class="btn btn-outline-secondary btn-lg px-5 border-0">Cancelar</a>
                                        <button type="submit" class="btn btn-primary btn-lg px-5 shadow-sm">
                                            <i class="bi bi-save-fill me-2"></i> Guardar Cambios
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection