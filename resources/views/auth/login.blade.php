<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión – Taller Aquino</title>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;600;700&family=Source+Sans+3:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Source Sans 3', sans-serif;
            min-height: 100vh;
            background: #0b1c2e;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        /* Fondo decorativo */
        body::before {
            content: '';
            position: fixed; inset: 0;
            background:
                radial-gradient(ellipse at 20% 50%, rgba(18,85,161,.25) 0%, transparent 60%),
                radial-gradient(ellipse at 80% 20%, rgba(46,141,255,.15) 0%, transparent 50%);
            pointer-events: none;
        }

        .login-wrapper {
            display: flex;
            width: 100%;
            max-width: 900px;
            min-height: 540px;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 30px 80px rgba(0,0,0,.5);
            position: relative;
            z-index: 1;
        }

        /* Panel izquierdo decorativo */
        .login-left {
            flex: 1;
            background: linear-gradient(145deg, #1255a1 0%, #0b1c2e 100%);
            padding: 48px 40px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
        }
        .login-left::after {
            content: '';
            position: absolute;
            bottom: -60px; right: -60px;
            width: 300px; height: 300px;
            border-radius: 50%;
            background: rgba(46,141,255,.08);
            border: 1px solid rgba(46,141,255,.1);
        }
        .login-left::before {
            content: '';
            position: absolute;
            top: -40px; left: -40px;
            width: 200px; height: 200px;
            border-radius: 50%;
            background: rgba(255,255,255,.03);
            border: 1px solid rgba(255,255,255,.05);
        }

        .brand { display: flex; align-items: center; gap: 14px; }
        .brand-icon {
            width: 48px; height: 48px; border-radius: 12px;
            background: white;
            display: flex; align-items: center; justify-content: center;
        }
        .brand-icon svg { width: 28px; height: 28px; }
        .brand-name {
            font-family: 'Oswald', sans-serif;
            font-size: 1.3rem; color: white;
            letter-spacing: .06em; line-height: 1.2;
        }
        .brand-name small { font-size: .65rem; color: rgba(255,255,255,.45); display: block; font-weight: 300; letter-spacing: .1em; }

        .left-tagline { position: relative; z-index: 1; }
        .left-tagline h2 {
            font-family: 'Oswald', sans-serif;
            font-size: 1.8rem; color: white;
            line-height: 1.3; margin-bottom: 12px;
        }
        .left-tagline p { font-size: .9rem; color: rgba(255,255,255,.5); line-height: 1.6; }

        .left-features { position: relative; z-index: 1; }
        .feature-item {
            display: flex; align-items: center; gap: 10px;
            padding: 8px 0;
            color: rgba(255,255,255,.65);
            font-size: .85rem;
        }
        .feature-item i { color: #2e8dff; font-size: 1rem; }

        /* Panel derecho - formulario */
        .login-right {
            width: 380px;
            background: white;
            padding: 48px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .login-right h1 {
            font-family: 'Oswald', sans-serif;
            font-size: 1.6rem; color: #0b1c2e;
            letter-spacing: .04em; margin-bottom: 6px;
        }
        .login-right p { font-size: .88rem; color: #5a7a95; margin-bottom: 32px; }

        .form-group { margin-bottom: 18px; }
        .form-label {
            display: block; font-size: .82rem;
            color: #0b1c2e; font-weight: 600;
            margin-bottom: 6px;
        }
        .input-wrap { position: relative; }
        .input-wrap i {
            position: absolute; left: 13px; top: 50%;
            transform: translateY(-50%);
            color: #5a7a95; font-size: .95rem;
        }
        .form-input {
            width: 100%; padding: 10px 13px 10px 38px;
            border: 1.5px solid #c0d3e8; border-radius: 9px;
            font-family: 'Source Sans 3', sans-serif;
            font-size: .92rem; color: #1a2b3c;
            outline: none; transition: border-color .18s;
            background: white;
        }
        .form-input:focus { border-color: #1255a1; box-shadow: 0 0 0 3px rgba(18,85,161,.1); }
        .form-input.error { border-color: #d93025; }

        .toggle-pass {
            position: absolute; right: 12px; top: 50%;
            transform: translateY(-50%);
            background: none; border: none; cursor: pointer;
            color: #5a7a95; font-size: .95rem; padding: 2px;
        }

        .error-msg { font-size: .78rem; color: #d93025; margin-top: 4px; }

        .remember-row {
            display: flex; justify-content: space-between; align-items: center;
            margin-bottom: 24px;
        }
        .remember-label {
            display: flex; align-items: center; gap: 8px;
            font-size: .84rem; color: #5a7a95; cursor: pointer;
        }
        .remember-label input { accent-color: #1255a1; width: 15px; height: 15px; }

        .btn-login {
            width: 100%; padding: 12px;
            background: #1255a1; color: white;
            border: none; border-radius: 9px; cursor: pointer;
            font-family: 'Oswald', sans-serif;
            font-size: 1rem; letter-spacing: .06em;
            transition: all .2s;
            box-shadow: 0 4px 14px rgba(18,85,161,.35);
        }
        .btn-login:hover { background: #1a6fcc; transform: translateY(-1px); }

        .divider {
            display: flex; align-items: center; gap: 12px;
            margin: 20px 0; color: #c0d3e8; font-size: .8rem;
        }
        .divider::before, .divider::after {
            content: ''; flex: 1; height: 1px; background: #e8f0f8;
        }

        .btn-register {
            width: 100%; padding: 10px;
            background: white; color: #1255a1;
            border: 1.5px solid #c0d3e8; border-radius: 9px; cursor: pointer;
            font-size: .88rem; text-decoration: none;
            display: block; text-align: center;
            transition: all .18s;
        }
        .btn-register:hover { border-color: #1255a1; background: #f0f5ff; color: #1255a1; }

        .consultar-link {
            display: block; text-align: center; margin-top: 16px;
            font-size: .8rem; color: #5a7a95; text-decoration: none;
        }
        .consultar-link:hover { color: #1255a1; }

        .alert-error {
            background: rgba(217,48,37,.08); border: 1px solid rgba(217,48,37,.25);
            border-radius: 9px; padding: 11px 14px;
            font-size: .86rem; color: #b02920;
            display: flex; align-items: center; gap: 9px;
            margin-bottom: 20px;
        }

        @media (max-width: 680px) {
            .login-left { display: none; }
            .login-right { width: 100%; border-radius: 20px; }
        }
    </style>
</head>
<body>

<div class="login-wrapper">
    {{-- Panel izquierdo --}}
    <div class="login-left">
        <div class="brand">
            <div class="brand-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="#1255a1" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="3"/>
                    <path d="M19.07 4.93a10 10 0 0 1 0 14.14M4.93 4.93a10 10 0 0 0 0 14.14"/>
                    <path d="M15.54 8.46a5 5 0 0 1 0 7.07M8.46 8.46a5 5 0 0 0 0 7.07"/>
                </svg>
            </div>
            <div class="brand-name">
                TALLER AQUINO
                <small>SISTEMA DE GESTIÓN</small>
            </div>
        </div>

        <div class="left-tagline">
            <h2>Gestión inteligente para tu taller</h2>
            <p>Control total de turnos, inventario, reparaciones y reportes desde un solo lugar.</p>
        </div>

        <div class="left-features">
            <div class="feature-item"><i class="bi bi-calendar-check"></i> Gestión de turnos online</div>
            <div class="feature-item"><i class="bi bi-box-seam"></i> Control de stock e inventario</div>
            <div class="feature-item"><i class="bi bi-wrench-adjustable"></i> Registro de reparaciones</div>
            <div class="feature-item"><i class="bi bi-file-earmark-bar-graph"></i> Reportes automáticos en PDF</div>
        </div>
    </div>

    {{-- Panel derecho --}}
    <div class="login-right">
        <h1>BIENVENIDO</h1>
        <p>Ingresá con tu cuenta para continuar</p>

        @if(session('error'))
        <div class="alert-error">
            <i class="bi bi-exclamation-triangle-fill"></i>
            {{ session('error') }}
        </div>
        @endif

        @if(session('success'))
        <div style="background:rgba(15,138,74,.08); border:1px solid rgba(15,138,74,.25); border-radius:9px; padding:11px 14px; font-size:.86rem; color:#0a6635; display:flex; align-items:center; gap:9px; margin-bottom:20px">
            <i class="bi bi-check-circle-fill"></i>{{ session('success') }}
        </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-group">
                <label class="form-label">Correo electrónico</label>
                <div class="input-wrap">
                    <i class="bi bi-envelope"></i>
                    <input type="email" name="email"
                        class="form-input {{ $errors->has('email') ? 'error' : '' }}"
                        value="{{ old('email') }}"
                        placeholder="correo@ejemplo.com" required autofocus>
                </div>
                @error('email')<div class="error-msg">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Contraseña</label>
                <div class="input-wrap">
                    <i class="bi bi-lock"></i>
                    <input type="password" name="password" id="passInput"
                        class="form-input {{ $errors->has('password') ? 'error' : '' }}"
                        placeholder="••••••••" required>
                    <button type="button" class="toggle-pass" onclick="togglePass()">
                        <i class="bi bi-eye" id="eyeIcon"></i>
                    </button>
                </div>
                @error('password')<div class="error-msg">{{ $message }}</div>@enderror
            </div>

            <div class="remember-row">
                <label class="remember-label">
                    <input type="checkbox" name="remember"> Recordarme
                </label>
            </div>

            <button type="submit" class="btn-login">
                <i class="bi bi-box-arrow-in-right" style="margin-right:8px"></i>INGRESAR
            </button>
        </form>

        <div class="divider">o</div>

        <a href="{{ route('register') }}" class="btn-register">
            Registrarme como cliente
        </a>

        <a href="{{ route('consultar.estado') }}" class="consultar-link">
            <i class="bi bi-search" style="margin-right:5px"></i>
            Consultar estado sin iniciar sesión
        </a>
    </div>
</div>

<script>
function togglePass() {
    const p = document.getElementById('passInput');
    const e = document.getElementById('eyeIcon');
    if (p.type === 'password') { p.type = 'text'; e.className = 'bi bi-eye-slash'; }
    else { p.type = 'password'; e.className = 'bi bi-eye'; }
}
</script>
</body>
</html>
