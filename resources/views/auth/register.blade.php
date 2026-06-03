<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrarse – Taller Aquino</title>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;600;700&family=Source+Sans+3:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Source Sans 3', sans-serif;
            min-height: 100vh;
            background: #0b1c2e;
            display: flex; align-items: center; justify-content: center;
            padding: 30px 20px;
        }
        body::before {
            content: '';
            position: fixed; inset: 0;
            background: radial-gradient(ellipse at 20% 50%, rgba(18,85,161,.2) 0%, transparent 60%);
            pointer-events: none;
        }

        .register-card {
            background: white;
            border-radius: 18px;
            width: 100%; max-width: 580px;
            overflow: hidden;
            box-shadow: 0 30px 80px rgba(0,0,0,.4);
            position: relative; z-index: 1;
        }

        .register-header {
            background: linear-gradient(135deg, #1255a1 0%, #0b1c2e 100%);
            padding: 28px 36px;
            display: flex; align-items: center; gap: 14px;
        }
        .header-icon {
            width: 44px; height: 44px; border-radius: 11px;
            background: white;
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        }
        .header-icon svg { width: 26px; height: 26px; }
        .header-text h1 {
            font-family: 'Oswald', sans-serif;
            font-size: 1.2rem; color: white; letter-spacing: .06em;
        }
        .header-text p { font-size: .8rem; color: rgba(255,255,255,.5); margin-top: 2px; }

        .register-body { padding: 32px 36px; }

        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .form-full { grid-column: span 2; }

        .form-group { margin-bottom: 0; }
        .form-label {
            display: block; font-size: .82rem; color: #0b1c2e;
            font-weight: 600; margin-bottom: 5px;
        }
        .req { color: #d93025; margin-left: 2px; }

        .form-input {
            width: 100%; padding: 9px 13px;
            border: 1.5px solid #c0d3e8; border-radius: 8px;
            font-family: 'Source Sans 3', sans-serif;
            font-size: .9rem; color: #1a2b3c; outline: none;
            transition: border-color .18s;
        }
        .form-input:focus { border-color: #1255a1; box-shadow: 0 0 0 3px rgba(18,85,161,.1); }
        .form-input.error { border-color: #d93025; }
        .error-msg { font-size: .76rem; color: #d93025; margin-top: 3px; }

        .errors-box {
            background: rgba(217,48,37,.07); border: 1px solid rgba(217,48,37,.2);
            border-radius: 9px; padding: 12px 16px; margin-bottom: 20px;
        }
        .errors-box ul { margin: 4px 0 0 16px; }
        .errors-box li { font-size: .83rem; color: #b02920; }

        .register-footer {
            padding: 20px 36px;
            border-top: 1px solid #e8f0f8;
            display: flex; align-items: center; justify-content: space-between; gap: 16px;
            flex-wrap: wrap;
        }

        .btn-submit {
            padding: 10px 28px;
            background: #1255a1; color: white;
            border: none; border-radius: 9px; cursor: pointer;
            font-family: 'Oswald', sans-serif;
            font-size: .92rem; letter-spacing: .05em;
            transition: all .2s;
            box-shadow: 0 3px 12px rgba(18,85,161,.3);
        }
        .btn-submit:hover { background: #1a6fcc; transform: translateY(-1px); }

        .login-link { font-size: .86rem; color: #5a7a95; }
        .login-link a { color: #1255a1; text-decoration: none; font-weight: 600; }
        .login-link a:hover { text-decoration: underline; }

        .pass-group { position: relative; }
        .toggle-pass {
            position: absolute; right: 10px; top: 50%;
            transform: translateY(-50%);
            background: none; border: none; cursor: pointer;
            color: #5a7a95; font-size: .9rem;
        }
    </style>
</head>
<body>

<div class="register-card">
    <div class="register-header">
        <div class="header-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="#1255a1" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="3"/>
                <path d="M19.07 4.93a10 10 0 0 1 0 14.14M4.93 4.93a10 10 0 0 0 0 14.14"/>
                <path d="M15.54 8.46a5 5 0 0 1 0 7.07M8.46 8.46a5 5 0 0 0 0 7.07"/>
            </svg>
        </div>
        <div class="header-text">
            <h1>CREAR CUENTA</h1>
            <p>Taller Aquino — Sistema de Gestión</p>
        </div>
    </div>

    <div class="register-body">
        @if($errors->any())
        <div class="errors-box">
            <strong style="font-size:.84rem; color:#b02920">Por favor corregí los siguientes errores:</strong>
            <ul>
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('register') }}">
            @csrf
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Nombre <span class="req">*</span></label>
                    <input type="text" name="name" class="form-input {{ $errors->has('name') ? 'error' : '' }}"
                        value="{{ old('name') }}" placeholder="Juan" required>
                    @error('name')<div class="error-msg">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Apellido <span class="req">*</span></label>
                    <input type="text" name="apellido" class="form-input {{ $errors->has('apellido') ? 'error' : '' }}"
                        value="{{ old('apellido') }}" placeholder="Pérez" required>
                    @error('apellido')<div class="error-msg">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">DNI <span class="req">*</span></label>
                    <input type="text" name="dni" class="form-input {{ $errors->has('dni') ? 'error' : '' }}"
                        value="{{ old('dni') }}" placeholder="35123456" required maxlength="20">
                    @error('dni')<div class="error-msg">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Teléfono <span class="req">*</span></label>
                    <input type="text" name="telefono" class="form-input {{ $errors->has('telefono') ? 'error' : '' }}"
                        value="{{ old('telefono') }}" placeholder="3751-000000" required>
                    @error('telefono')<div class="error-msg">{{ $message }}</div>@enderror
                </div>

                <div class="form-group form-full">
                    <label class="form-label">Correo electrónico <span class="req">*</span></label>
                    <input type="email" name="email" class="form-input {{ $errors->has('email') ? 'error' : '' }}"
                        value="{{ old('email') }}" placeholder="correo@ejemplo.com" required>
                    @error('email')<div class="error-msg">{{ $message }}</div>@enderror
                </div>

                <div class="form-group form-full">
                    <label class="form-label">Dirección</label>
                    <input type="text" name="direccion" class="form-input"
                        value="{{ old('direccion') }}" placeholder="Calle y número, ciudad">
                </div>

                <div class="form-group">
                    <label class="form-label">Contraseña <span class="req">*</span></label>
                    <div class="pass-group">
                        <input type="password" name="password" id="pass1"
                            class="form-input {{ $errors->has('password') ? 'error' : '' }}"
                            placeholder="Mínimo 8 caracteres" required style="padding-right:36px">
                        <button type="button" class="toggle-pass" onclick="togglePass('pass1','eye1')">
                            <i class="bi bi-eye" id="eye1"></i>
                        </button>
                    </div>
                    @error('password')<div class="error-msg">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Confirmar contraseña <span class="req">*</span></label>
                    <div class="pass-group">
                        <input type="password" name="password_confirmation" id="pass2"
                            class="form-input" placeholder="Repetí la contraseña" required style="padding-right:36px">
                        <button type="button" class="toggle-pass" onclick="togglePass('pass2','eye2')">
                            <i class="bi bi-eye" id="eye2"></i>
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="register-footer">
        <div class="login-link">
            ¿Ya tenés cuenta? <a href="{{ route('login') }}">Iniciá sesión</a>
        </div>
        <button type="submit" form="" onclick="document.querySelector('form').submit()" class="btn-submit">
            <i class="bi bi-person-plus" style="margin-right:8px"></i>CREAR CUENTA
        </button>
    </div>
</div>

<script>
function togglePass(id, iconId) {
    const p = document.getElementById(id);
    const e = document.getElementById(iconId);
    if (p.type === 'password') { p.type = 'text'; e.className = 'bi bi-eye-slash'; }
    else { p.type = 'password'; e.className = 'bi bi-eye'; }
}
</script>
</body>
</html>
