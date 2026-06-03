<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrarse – Taller Aquino</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'DM Sans', sans-serif; }
        body { background: linear-gradient(135deg, #2C3E50 0%, #1a252f 100%); min-height: 100vh; padding: 2rem 0; }
        .card { border: none; border-radius: 1rem; box-shadow: 0 20px 60px rgba(0,0,0,.3); }
        .register-header { background: #C0392B; padding: 1.5rem 2rem; color: #fff; border-radius: 1rem 1rem 0 0; }
        .register-header h1 { font-size: 1.4rem; font-weight: 700; margin: .4rem 0 .2rem; }
        .btn-taller { background: #C0392B; color: #fff; border: none; padding: .65rem; font-weight: 500; }
        .btn-taller:hover { background: #96281B; color: #fff; }
        .form-control:focus { border-color: #C0392B; box-shadow: 0 0 0 .2rem rgba(192,57,43,.2); }
        .required-mark { color: #C0392B; }
    </style>
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card">
                <div class="register-header d-flex align-items-center gap-3">
                    <i class="bi bi-tools fs-2"></i>
                    <div>
                        <h1>Taller Aquino</h1>
                        <p class="mb-0 opacity-75 small">Crear cuenta de cliente</p>
                    </div>
                </div>

                <div class="card-body p-4">
                    @if($errors->any())
                        <div class="alert alert-danger py-2 small">
                            <ul class="mb-0">
                                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <div class="row g-3">
                            <div class="col-sm-6">
                                <label class="form-label fw-semibold">Nombre <span class="required-mark">*</span></label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                    value="{{ old('name') }}" placeholder="Juan" required>
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label fw-semibold">Apellido <span class="required-mark">*</span></label>
                                <input type="text" name="apellido" class="form-control @error('apellido') is-invalid @enderror"
                                    value="{{ old('apellido') }}" placeholder="Pérez" required>
                                @error('apellido')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label fw-semibold">DNI <span class="required-mark">*</span></label>
                                <input type="text" name="dni" class="form-control @error('dni') is-invalid @enderror"
                                    value="{{ old('dni') }}" placeholder="35123456" required maxlength="20">
                                @error('dni')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label fw-semibold">Teléfono <span class="required-mark">*</span></label>
                                <input type="text" name="telefono" class="form-control @error('telefono') is-invalid @enderror"
                                    value="{{ old('telefono') }}" placeholder="3751-000000" required>
                                @error('telefono')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Correo electrónico <span class="required-mark">*</span></label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                    value="{{ old('email') }}" placeholder="correo@ejemplo.com" required>
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Dirección</label>
                                <input type="text" name="direccion" class="form-control"
                                    value="{{ old('direccion') }}" placeholder="Calle y número, ciudad">
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label fw-semibold">Contraseña <span class="required-mark">*</span></label>
                                <input type="password" name="password"
                                    class="form-control @error('password') is-invalid @enderror"
                                    placeholder="Mínimo 8 caracteres" required>
                                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label fw-semibold">Confirmar contraseña <span class="required-mark">*</span></label>
                                <input type="password" name="password_confirmation"
                                    class="form-control" placeholder="Repetí la contraseña" required>
                            </div>
                        </div>

                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-taller rounded-3">
                                <i class="bi bi-person-plus me-1"></i> Crear cuenta
                            </button>
                        </div>
                    </form>

                    <hr>
                    <p class="text-center text-muted small mb-0">
                        ¿Ya tenés cuenta?
                        <a href="{{ route('login') }}" class="text-decoration-none fw-semibold" style="color:#C0392B">
                            Iniciar sesión
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
