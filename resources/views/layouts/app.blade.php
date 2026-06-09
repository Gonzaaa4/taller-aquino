<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Taller Aquino') – Sistema de Gestión</title>

    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@300;400;600;700&family=Source+Sans+3:ital,wght@0,300;0,400;0,600;1,300&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
    :root {
        --navy:    #0b1c2e;
        --navy2:   #0f2540;
        --blue:    #1255a1;
        --blue2:   #1a6fcc;
        --accent:  #2e8dff;
        --silver:  #c8d4e0;
        --light:   #e8f0f8;
        --white:   #ffffff;
        --text:    #1a2b3c;
        --muted:   #5a7a95;
        --ok:      #0f8a4a;
        --ok2:     #12c968;
        --warn:    #e67e00;
        --error:   #d93025;
        --card:    #f4f8fc;
        --border:  #c0d3e8;
        --sidebar-w: 256px;
    }

    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    html { scroll-behavior: smooth; }

    body {
        font-family: 'Source Sans 3', sans-serif;
        background: #e2eaf3;
        color: var(--text);
        min-height: 100vh;
        display: flex;
        overflow-x: hidden;
    }

    /* ═══════════════════ SIDEBAR ═══════════════════ */
    #sidebar {
    width: var(--sidebar-w);
    height: 100vh;
    overflow: hidden;
    background: var(--navy);
    position: fixed;
    left: 0; top: 0;
    z-index: 200;
    display: flex;
    flex-direction: column;
    box-shadow: 4px 0 24px rgba(0,0,0,.25);
    transition: transform .3s cubic-bezier(.4,0,.2,1);
}

    /* accent stripe */
    #sidebar::before {
        content: '';
        position: absolute; top: 0; right: 0;
        width: 3px; height: 100%;
        background: linear-gradient(180deg, var(--accent) 0%, var(--blue) 60%, transparent 100%);
    }

    /* Logo */
    .sidebar-logo {
        padding: 20px 18px 16px;
        border-bottom: 1px solid rgba(255,255,255,.07);
        display: flex; align-items: center; gap: 12px;
        text-decoration: none;
    }
    .logo-mark {
        width: 40px; height: 40px; border-radius: 10px;
        background: var(--white);
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
        box-shadow: 0 3px 10px rgba(0,0,0,.3);
    }
    .logo-mark svg { width: 24px; height: 24px; }
    .logo-words h2 {
        font-family: 'Oswald', sans-serif;
        font-size: 1rem; color: var(--white);
        letter-spacing: .06em; line-height: 1.2;
    }
    .logo-words span {
        font-size: .62rem; color: rgba(255,255,255,.35);
        letter-spacing: .1em; text-transform: uppercase;
    }

    /* User pill */
    .sidebar-user {
        margin: 12px 12px 0;
        background: rgba(255,255,255,.06);
        border: 1px solid rgba(255,255,255,.08);
        border-radius: 10px; padding: 9px 12px;
        display: flex; align-items: center; gap: 9px;
    }
    .user-av {
        width: 30px; height: 30px; border-radius: 50%;
        background: var(--accent);
        display: flex; align-items: center; justify-content: center;
        font-family: 'Oswald', sans-serif; font-size: .78rem; color: white;
        flex-shrink: 0; font-weight: 600;
    }
    .user-info small { display: block; font-size: .6rem; color: rgba(255,255,255,.35); text-transform: uppercase; letter-spacing: .07em; }
    .user-info strong { font-size: .8rem; color: rgba(255,255,255,.82); font-weight: 400; }
    .user-role {
        margin-left: auto;
        font-size: .6rem; background: rgba(46,141,255,.2);
        color: var(--accent); border-radius: 20px; padding: 2px 8px;
        border: 1px solid rgba(46,141,255,.25);
        white-space: nowrap;
    }

    /* Nav */
    .sidebar-nav { padding: 14px 10px; flex: 1; overflow-y: scroll; }
    .nav-section-label {
        font-size: .6rem; color: rgba(255,255,255,.25);
        letter-spacing: .1em; text-transform: uppercase;
        padding: 14px 10px 5px;
    }

    .nav-item {
        display: flex; align-items: center; gap: 10px;
        padding: 9px 11px; border-radius: 8px;
        cursor: pointer; margin-bottom: 1px;
        text-decoration: none;
        transition: all .18s;
        position: relative; overflow: hidden;
        color: rgba(255,255,255,.6);
    }
    .nav-item::before {
        content: ''; position: absolute; left: 0; top: 0;
        width: 3px; height: 100%; border-radius: 0 2px 2px 0;
        background: var(--accent); transform: scaleY(0);
        transition: transform .2s;
    }
    .nav-item:hover { background: rgba(255,255,255,.07); color: white; }
    .nav-item:hover::before { transform: scaleY(1); }
    .nav-item.active { background: rgba(46,141,255,.14); color: white; }
    .nav-item.active::before { transform: scaleY(1); }

    .nav-icon {
        width: 30px; height: 30px; border-radius: 7px;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0; background: rgba(255,255,255,.07);
        font-size: .9rem;
        transition: background .18s;
    }
    .nav-item:hover .nav-icon,
    .nav-item.active .nav-icon { background: rgba(46,141,255,.2); }

    .nav-label { font-size: .86rem; }
    .nav-badge {
        margin-left: auto; font-size: .62rem; font-weight: 700;
        background: var(--accent); color: white;
        border-radius: 20px; padding: 1px 7px;
    }
    .nav-badge.warn { background: var(--warn); }
    .nav-badge.danger { background: var(--error); }

    /* Sidebar footer (logout) */
    .sidebar-footer {
        padding: 12px; border-top: 1px solid rgba(255,255,255,.07);
    }
    .btn-logout {
        display: flex; align-items: center; gap: 9px;
        width: 100%; padding: 9px 12px; border-radius: 8px;
        background: none; border: none; cursor: pointer;
        color: rgba(255,255,255,.4); font-size: .84rem;
        font-family: 'Source Sans 3', sans-serif;
        transition: all .18s;
    }
    .btn-logout:hover { background: rgba(217,48,37,.15); color: #ff6b6b; }

    /* ═══════════════════ MAIN ═══════════════════ */
    #main-content {
        margin-left: var(--sidebar-w);
        flex: 1; min-height: 100vh;
        display: flex; flex-direction: column;
    }

    /* Topbar */
    #topbar {
        background: var(--white);
        border-bottom: 1px solid var(--border);
        padding: 0 28px;
        height: 58px;
        display: flex; align-items: center; gap: 16px;
        position: sticky; top: 0; z-index: 100;
        box-shadow: 0 2px 8px rgba(0,0,0,.06);
    }
    .topbar-title {
        font-family: 'Oswald', sans-serif;
        font-size: 1rem; color: var(--navy);
        letter-spacing: .03em;
    }
    .topbar-title span { color: var(--blue2); }
    .topbar-date { font-size: .78rem; color: var(--muted); }

    .topbar-btn {
        width: 36px; height: 36px; border-radius: 9px;
        border: 1.5px solid var(--border);
        display: flex; align-items: center; justify-content: center;
        cursor: pointer; color: var(--muted); text-decoration: none;
        font-size: 1rem; position: relative;
        transition: all .18s;
    }
    .topbar-btn:hover { background: var(--light); border-color: var(--blue); color: var(--blue); }

    .alert-dot {
        position: absolute; top: 4px; right: 4px;
        width: 8px; height: 8px; border-radius: 50%;
        background: var(--error); border: 2px solid white;
    }

    /* Page content */
    .page-content { padding: 28px 28px 48px; flex: 1; }

    /* ═══════════════════ COMPONENTES ═══════════════════ */

    /* Page header */
    .page-header { margin-bottom: 24px; }
    .page-header-top { display: flex; align-items: flex-start; justify-content: space-between; gap: 16px; flex-wrap: wrap; }
    .page-eyebrow { font-size: .7rem; color: var(--muted); letter-spacing: .1em; text-transform: uppercase; margin-bottom: 4px; }
    .page-title {
        font-family: 'Oswald', sans-serif;
        font-size: 1.6rem; color: var(--navy); letter-spacing: .03em; line-height: 1.2;
    }
    .page-subtitle { font-size: .88rem; color: var(--muted); margin-top: 3px; }

    /* Cards */
    .ta-card {
        background: var(--white);
        border-radius: 12px;
        border: 1px solid var(--border);
        box-shadow: 0 2px 8px rgba(0,0,0,.05);
        overflow: hidden;
    }
    .ta-card-header {
        padding: 14px 20px;
        border-bottom: 1px solid var(--border);
        display: flex; align-items: center; justify-content: space-between;
        gap: 12px;
    }
    .ta-card-title {
        font-family: 'Oswald', sans-serif;
        font-size: .95rem; color: var(--navy); letter-spacing: .04em;
        display: flex; align-items: center; gap: 8px;
    }
    .ta-card-body { padding: 20px; }

    /* KPI Cards */
    .kpi-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 24px; }
    .kpi-card {
        background: var(--white);
        border: 1px solid var(--border);
        border-radius: 12px; padding: 18px 20px;
        display: flex; align-items: center; gap: 15px;
        box-shadow: 0 2px 8px rgba(0,0,0,.05);
        position: relative; overflow: hidden;
    }
    .kpi-card::after {
        content: ''; position: absolute; top: 0; left: 0;
        width: 100%; height: 3px;
    }
    .kpi-blue::after   { background: var(--accent); }
    .kpi-green::after  { background: var(--ok2); }
    .kpi-orange::after { background: var(--warn); }
    .kpi-red::after    { background: var(--error); }

    .kpi-icon {
        width: 46px; height: 46px; border-radius: 11px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.3rem; flex-shrink: 0;
    }
    .kpi-blue   .kpi-icon { background: rgba(46,141,255,.12); color: var(--accent); }
    .kpi-green  .kpi-icon { background: rgba(18,201,104,.12); color: var(--ok); }
    .kpi-orange .kpi-icon { background: rgba(230,126,0,.12);  color: var(--warn); }
    .kpi-red    .kpi-icon { background: rgba(217,48,37,.12);  color: var(--error); }

    .kpi-data { flex: 1; }
    .kpi-label { font-size: .75rem; color: var(--muted); text-transform: uppercase; letter-spacing: .07em; }
    .kpi-value { font-family: 'Oswald', sans-serif; font-size: 1.9rem; color: var(--navy); line-height: 1.1; }

    /* Buttons */
    .btn-primary-ta {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 9px 20px; border-radius: 9px;
        background: var(--blue); color: white;
        border: none; cursor: pointer; text-decoration: none;
        font-family: 'Oswald', sans-serif; font-size: .88rem;
        letter-spacing: .05em;
        transition: all .2s;
        box-shadow: 0 3px 10px rgba(18,85,161,.3);
    }
    .btn-primary-ta:hover { background: var(--blue2); color: white; transform: translateY(-1px); }

    .btn-secondary-ta {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 9px 18px; border-radius: 9px;
        background: var(--white); color: var(--navy);
        border: 1.5px solid var(--border); cursor: pointer; text-decoration: none;
        font-family: 'Source Sans 3', sans-serif; font-size: .88rem;
        transition: all .18s;
    }
    .btn-secondary-ta:hover { border-color: var(--blue); color: var(--blue); background: var(--light); }

    .btn-ok-ta {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 9px 20px; border-radius: 9px;
        background: var(--ok); color: white;
        border: none; cursor: pointer; text-decoration: none;
        font-family: 'Oswald', sans-serif; font-size: .88rem;
        letter-spacing: .05em;
        transition: all .2s;
    }
    .btn-ok-ta:hover { background: #0a7a42; color: white; transform: translateY(-1px); }

    .btn-danger-ta {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 8px 16px; border-radius: 9px;
        background: rgba(217,48,37,.1); color: var(--error);
        border: 1.5px solid rgba(217,48,37,.25); cursor: pointer; text-decoration: none;
        font-size: .86rem; transition: all .18s;
    }
    .btn-danger-ta:hover { background: var(--error); color: white; border-color: var(--error); }

    /* Tables */
    .ta-table { width: 100%; border-collapse: collapse; }
    .ta-table thead th {
        padding: 11px 16px;
        background: var(--light);
        color: var(--muted); font-size: .72rem;
        text-transform: uppercase; letter-spacing: .07em;
        font-weight: 600; border-bottom: 1px solid var(--border);
        white-space: nowrap;
    }
    .ta-table tbody td {
        padding: 13px 16px;
        border-bottom: 1px solid rgba(192,211,232,.4);
        vertical-align: middle;
        font-size: .9rem;
    }
    .ta-table tbody tr:hover td { background: var(--card); }
    .ta-table tbody tr:last-child td { border-bottom: none; }

    /* Badges */
    .ta-badge {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 3px 10px; border-radius: 20px;
        font-size: .72rem; font-weight: 600; white-space: nowrap;
    }
    .ta-badge::before { content: ''; width: 6px; height: 6px; border-radius: 50%; flex-shrink: 0; }
    .badge-pendiente   { background: rgba(230,126,0,.12);  color: var(--warn); }
    .badge-pendiente::before { background: var(--warn); }
    .badge-confirmado  { background: rgba(46,141,255,.12); color: var(--accent); }
    .badge-confirmado::before { background: var(--accent); }
    .badge-en_proceso  { background: rgba(18,85,161,.12);  color: var(--blue); }
    .badge-en_proceso::before { background: var(--blue); }
    .badge-finalizado  { background: rgba(15,138,74,.12);  color: var(--ok); }
    .badge-finalizado::before { background: var(--ok); }
    .badge-cancelado   { background: rgba(217,48,37,.1);   color: var(--error); }
    .badge-cancelado::before { background: var(--error); }
    .badge-entregado   { background: rgba(90,122,149,.12); color: var(--muted); }
    .badge-entregado::before { background: var(--muted); }
    .badge-ingresado   { background: rgba(46,141,255,.12); color: var(--accent); }
    .badge-ingresado::before { background: var(--accent); }
    .badge-en_diagnostico { background: rgba(230,126,0,.12); color: var(--warn); }
    .badge-en_diagnostico::before { background: var(--warn); }
    .badge-en_reparacion  { background: rgba(18,85,161,.12); color: var(--blue); }
    .badge-en_reparacion::before { background: var(--blue); }

    /* Stock badges */
    .stock-ok      { background: rgba(15,138,74,.1);  color: var(--ok); border-radius: 6px; padding: 2px 8px; font-size: .75rem; font-weight: 600; }
    .stock-critico { background: rgba(230,126,0,.12); color: var(--warn); border-radius: 6px; padding: 2px 8px; font-size: .75rem; font-weight: 600; }
    .stock-sin     { background: rgba(217,48,37,.1);  color: var(--error); border-radius: 6px; padding: 2px 8px; font-size: .75rem; font-weight: 600; }

    /* Forms */
    .ta-form-group { margin-bottom: 16px; }
    .ta-label { display: block; font-size: .82rem; color: var(--navy); font-weight: 600; margin-bottom: 5px; }
    .ta-label .req { color: var(--error); margin-left: 2px; }
    .ta-input {
        width: 100%; padding: 9px 13px; border-radius: 8px;
        border: 1.5px solid var(--border);
        background: var(--white); color: var(--text);
        font-family: 'Source Sans 3', sans-serif; font-size: .92rem;
        transition: border-color .18s;
        outline: none;
    }
    .ta-input:focus { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(46,141,255,.12); }
    .ta-input.is-invalid { border-color: var(--error); }
    .ta-select { appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24'%3E%3Cpath fill='%235a7a95' d='M7 10l5 5 5-5z'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 12px center; padding-right: 36px; }
    .ta-textarea { resize: vertical; min-height: 80px; }
    .ta-invalid-msg { font-size: .78rem; color: var(--error); margin-top: 4px; }

    /* Alerts */
    .ta-alert {
        border-radius: 10px; padding: 13px 16px;
        display: flex; align-items: flex-start; gap: 12px;
        font-size: .9rem; margin-bottom: 20px;
        border: 1px solid;
    }
    .ta-alert-icon { font-size: 1.1rem; flex-shrink: 0; margin-top: 1px; }
    .ta-alert.success { background: rgba(15,138,74,.08);  border-color: rgba(15,138,74,.3);  color: #0a6635; }
    .ta-alert.error   { background: rgba(217,48,37,.08);  border-color: rgba(217,48,37,.3);  color: #b02920; }
    .ta-alert.warning { background: rgba(230,126,0,.08);  border-color: rgba(230,126,0,.3);  color: #a85e00; }
    .ta-alert.info    { background: rgba(46,141,255,.08); border-color: rgba(46,141,255,.3); color: var(--blue); }

    /* Nro de seguimiento */
    .nro-seguimiento {
        font-family: 'Oswald', sans-serif;
        letter-spacing: .08em; color: var(--accent);
        font-size: 1.1rem;
    }

    /* Section divider */
    .section-label {
        display: flex; align-items: center; gap: 12px; margin-bottom: 16px;
    }
    .section-label h2 {
        font-family: 'Oswald', sans-serif;
        font-size: 1.1rem; color: var(--navy); letter-spacing: .04em; white-space: nowrap;
    }
    .section-label-line { flex: 1; height: 1px; background: var(--border); }

    /* Pagination */
    .pagination { display: flex; gap: 4px; flex-wrap: wrap; }
    .page-link {
        padding: 6px 12px; border-radius: 7px;
        border: 1.5px solid var(--border); color: var(--navy);
        text-decoration: none; font-size: .85rem;
        transition: all .18s;
    }
    .page-link:hover { border-color: var(--blue); color: var(--blue); background: var(--light); }
    .page-item.active .page-link { background: var(--blue); border-color: var(--blue); color: white; }
    .page-item.disabled .page-link { opacity: .4; pointer-events: none; }

    /* Mobile */
    #sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,.5); z-index: 199; }
    .btn-menu { display: none; background: none; border: none; font-size: 1.3rem; color: var(--navy); cursor: pointer; }

    @media (max-width: 992px) {
        #sidebar { transform: translateX(-100%); }
        #sidebar.open { transform: translateX(0); }
        #sidebar-overlay.open { display: block; }
        #main-content { margin-left: 0; }
        .btn-menu { display: flex; align-items: center; }
        .page-content { padding: 20px 16px 40px; }
        .kpi-grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 480px) {
        .kpi-grid { grid-template-columns: 1fr; }
    }
    </style>
    @stack('styles')
</head>
<body>

{{-- Sidebar overlay mobile --}}
<div id="sidebar-overlay" onclick="closeSidebar()"></div>

{{-- ═══════════════ SIDEBAR ═══════════════ --}}
<nav id="sidebar">
    {{-- Logo --}}
    <a class="sidebar-logo" href="{{ auth()->check() ? (auth()->user()->esCliente() ? route('cliente.dashboard') : route('admin.dashboard')) : route('home') }}">
        <div class="logo-mark">
            <svg viewBox="0 0 24 24" fill="none" stroke="#1255a1" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="3"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14M4.93 4.93a10 10 0 0 0 0 14.14"/>
                <path d="M15.54 8.46a5 5 0 0 1 0 7.07M8.46 8.46a5 5 0 0 0 0 7.07"/>
            </svg>
        </div>
        <div class="logo-words">
            <h2>TALLER AQUINO</h2>
            <span>Sistema de Gestión</span>
        </div>
    </a>

    @auth
    {{-- User pill --}}
    <div class="sidebar-user">
        <div class="user-av">{{ strtoupper(substr(auth()->user()->name, 0, 1) . substr(auth()->user()->apellido, 0, 1)) }}</div>
        <div class="user-info">
            <small>Conectado</small>
            <strong>{{ auth()->user()->name }} {{ auth()->user()->apellido }}</strong>
        </div>
        <div class="user-role">{{ ucfirst(auth()->user()->rol) }}</div>
    </div>

    {{-- Nav --}}
    <div class="sidebar-nav">
        @if(auth()->user()->esCliente())
            {{-- CLIENTE --}}
            <div class="nav-section-label">Mi cuenta</div>
            <a href="{{ route('cliente.dashboard') }}" class="nav-item {{ request()->routeIs('cliente.dashboard') ? 'active' : '' }}">
                <div class="nav-icon"><i class="bi bi-house"></i></div>
                <span class="nav-label">Inicio</span>
            </a>
            <a href="{{ route('cliente.turnos.index') }}" class="nav-item {{ request()->routeIs('cliente.turnos.*') ? 'active' : '' }}">
                <div class="nav-icon"><i class="bi bi-calendar-check"></i></div>
                <span class="nav-label">Mis Turnos</span>
            </a>
            <a href="{{ route('cliente.turnos.solicitar') }}" class="nav-item">
                <div class="nav-icon"><i class="bi bi-plus-circle"></i></div>
                <span class="nav-label">Solicitar Turno</span>
            </a>
            <a href="{{ route('cliente.vehiculos.index') }}" class="nav-item {{ request()->routeIs('cliente.vehiculos.*') ? 'active' : '' }}">
                <div class="nav-icon"><i class="bi bi-car-front"></i></div>
                <span class="nav-label">Mis Vehículos</span>
            </a>
            <a href="{{ route('cliente.consultar-estado') }}" class="nav-item {{ request()->routeIs('cliente.consultar-estado') ? 'active' : '' }}">
                <div class="nav-icon"><i class="bi bi-search"></i></div>
                <span class="nav-label">Estado de Reparación</span>
            </a>
        @else
            {{-- ADMIN / EMPLEADO --}}
            <div class="nav-section-label">Principal</div>
            <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <div class="nav-icon"><i class="bi bi-speedometer2"></i></div>
                <span class="nav-label">Dashboard</span>
            </a>

            <div class="nav-section-label">Operaciones</div>
            <a href="{{ route('admin.turnos.agenda') }}" class="nav-item {{ request()->routeIs('admin.turnos.agenda') ? 'active' : '' }}">
                <div class="nav-icon"><i class="bi bi-calendar-week"></i></div>
                <span class="nav-label">Agenda</span>
            </a>
            <a href="{{ route('admin.turnos.index') }}" class="nav-item {{ request()->routeIs('admin.turnos.index') || request()->routeIs('admin.turnos.show') ? 'active' : '' }}">
                <div class="nav-icon"><i class="bi bi-calendar-check"></i></div>
                <span class="nav-label">Turnos</span>
                @php $pendientes = \App\Models\Turno::where('estado','pendiente')->count(); @endphp
                @if($pendientes > 0)<span class="nav-badge">{{ $pendientes }}</span>@endif
            </a>
            <a href="{{ route('admin.trabajos.index') }}" class="nav-item {{ request()->routeIs('admin.trabajos.*') ? 'active' : '' }}">
                <div class="nav-icon"><i class="bi bi-wrench-adjustable"></i></div>
                <span class="nav-label">Órdenes de Trabajo</span>
            </a>

            <div class="nav-section-label">Inventario</div>
            <a href="{{ route('admin.inventario.repuestos') }}" class="nav-item {{ request()->routeIs('admin.inventario.repuesto*') ? 'active' : '' }}">
                <div class="nav-icon"><i class="bi bi-box-seam"></i></div>
                <span class="nav-label">Inventario</span>
                @php $alertas = \App\Models\Repuesto::activo()->conStockBajo()->count(); @endphp
                @if($alertas > 0)<span class="nav-badge warn">{{ $alertas }}</span>@endif
            </a>

            <div class="nav-section-label">Finanzas</div>
            <a href="{{ route('admin.facturacion.index') }}" class="nav-item {{ request()->routeIs('admin.facturacion.index') || request()->routeIs('admin.facturacion.show') || request()->routeIs('admin.facturacion.crear') ? 'active' : '' }}">
                <div class="nav-icon"><i class="bi bi-receipt"></i></div>
                <span class="nav-label">Facturación</span>
            </a>
            <a href="{{ route('admin.facturacion.caja') }}" class="nav-item {{ request()->routeIs('admin.facturacion.caja') ? 'active' : '' }}">
                <div class="nav-icon"><i class="bi bi-cash-stack"></i></div>
                <span class="nav-label">Caja</span>
            </a>

            <div class="nav-section-label">Reportes</div>
            <a href="{{ route('admin.reportes.formulario') }}" class="nav-item {{ request()->routeIs('admin.reportes.*') ? 'active' : '' }}">
                <div class="nav-icon"><i class="bi bi-file-earmark-bar-graph"></i></div>
                <span class="nav-label">Generar Reporte</span>
            </a>

            @if(auth()->user()->esAdmin())
            <div class="nav-section-label">Administración</div>
            <a href="{{ route('admin.usuarios.index') }}" class="nav-item {{ request()->routeIs('admin.usuarios.*') ? 'active' : '' }}">
                <div class="nav-icon"><i class="bi bi-people"></i></div>
                <span class="nav-label">Usuarios</span>
            </a>
            @endif
        @endif
    </div>

    {{-- Footer logout --}}
    <div class="sidebar-footer">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn-logout">
                <i class="bi bi-box-arrow-left"></i> Cerrar sesión
            </button>
        </form>
    </div>
    @endauth
</nav>

{{-- ═══════════════ MAIN ═══════════════ --}}
<div id="main-content">
    {{-- Topbar --}}
    <div id="topbar">
        <button class="btn-menu" onclick="openSidebar()"><i class="bi bi-list"></i></button>

        <div style="flex:1">
            <div class="topbar-title">
                @isset($__env)
                @yield('topbar-title', 'Taller Aquino')
                @endisset
            </div>
        </div>

        @auth
        @if(auth()->user()->esEmpleado())
            @php $alertasStock = \App\Models\Repuesto::activo()->conStockBajo()->count(); @endphp
            @if($alertasStock > 0)
            <a href="{{ route('admin.inventario.repuestos', ['stock_bajo' => 1]) }}" class="topbar-btn" title="{{ $alertasStock }} repuestos con stock bajo">
                <i class="bi bi-exclamation-triangle"></i>
                <span class="alert-dot"></span>
            </a>
            @endif
        @endif
        @endauth

        <div class="topbar-date">
            {{ now()->locale('es')->isoFormat('ddd D MMM, YYYY') }}
        </div>
    </div>

    {{-- Page content --}}
    <div class="page-content">

        {{-- Flash alerts --}}
        @if(session('success'))
        <div class="ta-alert success">
            <span class="ta-alert-icon"><i class="bi bi-check-circle-fill"></i></span>
            <div>{{ session('success') }}</div>
        </div>
        @endif
        @if(session('error'))
        <div class="ta-alert error">
            <span class="ta-alert-icon"><i class="bi bi-exclamation-triangle-fill"></i></span>
            <div>{{ session('error') }}</div>
        </div>
        @endif
        @if($errors->any())
        <div class="ta-alert error">
            <span class="ta-alert-icon"><i class="bi bi-exclamation-triangle-fill"></i></span>
            <div>
                <strong>Corregí los siguientes errores:</strong>
                <ul style="margin:.4rem 0 0 1rem">
                    @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                </ul>
            </div>
        </div>
        @endif

        @yield('content')
    </div>
</div>

<script>
function openSidebar() {
    document.getElementById('sidebar').classList.add('open');
    document.getElementById('sidebar-overlay').classList.add('open');
}
function closeSidebar() {
    document.getElementById('sidebar').classList.remove('open');
    document.getElementById('sidebar-overlay').classList.remove('open');
}
</script>

@stack('scripts')
</body>
</html>
