<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión – Taller Aquino</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&family=DM+Mono:wght@500&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'DM Sans', sans-serif; }
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #2C3E50 0%, #1a252f 100%);
            display: flex;
            align-items: center;
        }
        .login-card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 20px 60px rgba(0,0,0,.3);
            overflow: hidden;
        }
        .login-header {
            background: #C0392B;
            padding: 2rem;
            text-align: center;
            color: #fff;
        }
        .login-header h1 { font-size: 1.5rem; font-weight: 700; margin: .5rem 0 .25rem; }
        .login-header p  { opacity: .8; font-size: .875rem; margin: 0; }
        .login-body { padding: 2rem; }
        .btn-taller {
            background: #C0392B; color: #fff; border: none;
            padding: .65rem; font-weight: 500;
        }
        .btn-taller:hover { background: #96281B; color: #fff; }
        .form-control:focus { border-color: #C0392B; box-shadow: 0 0 0 .2rem rgba(192,57,43,.2); }
    </style>
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-sm-10 col-md-7 col-lg-5 col-xl-4">

            <div class="login-card card">
                <div class="login-header">
                    <i class="bi bi-tools fs-1"></i>
                    <h1>Taller Aquino</h1>
                    <p>Sistema de Gestión</p>
                </div>

                <div class="login-body">
                    @if(session('error'))
                        <div class="alert alert-danger py-2 small">
                            <i class="bi bi-exclamation-triangle me-1"></i>{{ session('error') }}
                        </div>
                    @endif
                    @if(session('success'))
                        <div class="alert alert-success py-2 small">{{ session('success') }}</div>
                    @endif

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Correo electrónico</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                    value="{{ old('email') }}" placeholder="correo@ejemplo.com" required autofocus>
                            </div>
                            @error('email')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <label class="form-label fw-semibold">Contraseña</label>
                            </div>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                <input type="password" name="password" id="password"
                                    class="form-control @error('password') is-invalid @enderror"
                                    placeholder="••••••••" required>
                                <button type="button" class="btn btn-outline-secondary" onclick="togglePass()">
                                    <i class="bi bi-eye" id="eye-icon"></i>
                                </button>
                            </div>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" name="remember" id="remember">
                            <label class="form-check-label small" for="remember">Recordarme</label>
                        </div>

                        <button type="submit" class="btn btn-taller w-100 rounded-3">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Ingresar
                        </button>
                    </form>

                    <hr class="my-3">
                    <p class="text-center text-muted small mb-2">¿No tenés cuenta?</p>
                    <a href="{{ route('register') }}" class="btn btn-outline-secondary w-100 btn-sm">
                        Registrarme como cliente
                    </a>

                    <div class="text-center mt-3">
                        <a href="{{ route('consultar.estado') }}" class="text-muted small">
                            <i class="bi bi-search me-1"></i>Consultar estado sin iniciar sesión
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<script>
function togglePass() {
    const p = document.getElementById('password');
    const e = document.getElementById('eye-icon');
    if (p.type === 'password') { p.type = 'text'; e.className = 'bi bi-eye-slash'; }
    else { p.type = 'password'; e.className = 'bi bi-eye'; }
}
</script>
</body>
</html>
