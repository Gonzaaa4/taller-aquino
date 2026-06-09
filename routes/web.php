<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\TurnoController as AdminTurnoController;
use App\Http\Controllers\Admin\InventarioController;
use App\Http\Controllers\Admin\TrabajoController;
use App\Http\Controllers\Admin\ReporteController;
use App\Http\Controllers\Admin\ProveedorController;
use App\Http\Controllers\Admin\UsuarioController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Cliente\TurnoController as ClienteTurnoController;
use App\Http\Controllers\Cliente\DashboardController as ClienteDashboard;
use App\Http\Controllers\Cliente\VehiculoController;

// ── Página de inicio ─────────────────────────────────────────────
Route::get('/', function () {
    if (auth()->check()) {
        return auth()->user()->esCliente()
            ? redirect()->route('cliente.dashboard')
            : redirect()->route('admin.dashboard');
    }
    return redirect()->route('login');
})->name('home');

// ── Autenticación ────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',    [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',   [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register',[AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ── Panel de ADMINISTRACIÓN ──────────────────────────────────────
// Accesible para: admin, administrativo, mecanico
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'role:admin,administrativo,mecanico'])
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // ── Turnos ──────────────────────────────────────────────
        Route::prefix('turnos')->name('turnos.')->group(function () {
            Route::get('/',                        [AdminTurnoController::class, 'index'])->name('index');
            Route::get('/agenda',                  [AdminTurnoController::class, 'agenda'])->name('agenda');
            Route::get('/solicitar',               [AdminTurnoController::class, 'solicitar'])->name('solicitar');
            Route::post('/solicitar',              [AdminTurnoController::class, 'guardar'])->name('guardar');
            Route::get('/{turno}',                 [AdminTurnoController::class, 'show'])->name('show');
            Route::post('/{turno}/confirmar',      [AdminTurnoController::class, 'confirmar'])->name('confirmar');
            Route::post('/{turno}/cancelar',       [AdminTurnoController::class, 'cancelar'])->name('cancelar');
            Route::post('/{turno}/asignar-mecanico',[AdminTurnoController::class,'asignarMecanico'])->name('asignar-mecanico');
        });

        // ── Trabajos / Órdenes de trabajo ────────────────────────
        Route::prefix('trabajos')->name('trabajos.')->group(function () {
            Route::get('/',                              [TrabajoController::class, 'index'])->name('index');
            Route::post('/ingreso',                      [TrabajoController::class, 'registrarIngreso'])->name('ingreso');
            Route::get('/{ingreso}',                     [TrabajoController::class, 'show'])->name('show');
            Route::post('/{ingreso}/trabajo',            [TrabajoController::class, 'guardarTrabajo'])->name('guardar-trabajo');
            Route::post('/{ingreso}/egreso',             [TrabajoController::class, 'registrarEgreso'])->name('egreso');
        });

        // ── Facturación y Caja ───────────────────────────────────
        Route::prefix('facturacion')->name('facturacion.')->group(function () {
            Route::get('/',                          [App\Http\Controllers\Admin\FacturacionController::class, 'index'])->name('index');
            Route::get('/caja',                      [App\Http\Controllers\Admin\FacturacionController::class, 'caja'])->name('caja');
            Route::post('/caja/movimiento',          [App\Http\Controllers\Admin\FacturacionController::class, 'guardarMovimiento'])->name('caja.movimiento');
            Route::get('/crear/{ingreso}',           [App\Http\Controllers\Admin\FacturacionController::class, 'crear'])->name('crear');
            Route::post('/crear/{ingreso}',          [App\Http\Controllers\Admin\FacturacionController::class, 'guardar'])->name('guardar');
            Route::get('/{factura}',                 [App\Http\Controllers\Admin\FacturacionController::class, 'show'])->name('show');
            Route::post('/{factura}/pago',           [App\Http\Controllers\Admin\FacturacionController::class, 'registrarPago'])->name('pago');
            Route::post('/{factura}/anular',         [App\Http\Controllers\Admin\FacturacionController::class, 'anular'])->name('anular');
        });

        // ── Compras ──────────────────────────────────────────────
        Route::prefix('compras')->name('compras.')->group(function () {
            Route::get('/',                              [App\Http\Controllers\Admin\OrdenCompraController::class, 'index'])->name('index');
            Route::get('/crear',                         [App\Http\Controllers\Admin\OrdenCompraController::class, 'crear'])->name('crear');
            Route::post('/',                             [App\Http\Controllers\Admin\OrdenCompraController::class, 'guardar'])->name('guardar');
            Route::get('/{orden}',                       [App\Http\Controllers\Admin\OrdenCompraController::class, 'show'])->name('show');
            Route::post('/{orden}/recibir',              [App\Http\Controllers\Admin\OrdenCompraController::class, 'recibirMercaderia'])->name('recibir');
            Route::post('/{orden}/cancelar',             [App\Http\Controllers\Admin\OrdenCompraController::class, 'cancelar'])->name('cancelar');
        });

        // ── Contabilidad ─────────────────────────────────────────
        Route::prefix('contabilidad')->name('contabilidad.')->group(function () {
            Route::get('/libro',        [App\Http\Controllers\Admin\ContabilidadController::class, 'libro'])->name('libro');
            Route::get('/rentabilidad', [App\Http\Controllers\Admin\ContabilidadController::class, 'rentabilidad'])->name('rentabilidad');
            Route::get('/margen',       [App\Http\Controllers\Admin\ContabilidadController::class, 'margen'])->name('margen');
        });

        // ── RRHH ─────────────────────────────────────────────────
        Route::prefix('rrhh')->name('rrhh.')->group(function () {
            Route::get('/',                                      [App\Http\Controllers\Admin\RRHHController::class, 'index'])->name('index');
            Route::get('/{mecanico}',                            [App\Http\Controllers\Admin\RRHHController::class, 'perfil'])->name('perfil');
            Route::post('/{mecanico}/horas',                     [App\Http\Controllers\Admin\RRHHController::class, 'registrarHoras'])->name('horas');
            Route::delete('/horas/{hora}',                       [App\Http\Controllers\Admin\RRHHController::class, 'eliminarHoras'])->name('horas.eliminar');
            Route::post('/{mecanico}/comision',                  [App\Http\Controllers\Admin\RRHHController::class, 'registrarComision'])->name('comision');
            Route::post('/comisiones/{comision}/pagar',          [App\Http\Controllers\Admin\RRHHController::class, 'pagarComision'])->name('comision.pagar');
        });

        // ── Inventario ───────────────────────────────────────────
        Route::prefix('inventario')->name('inventario.')->group(function () {
            // Repuestos
            Route::get('/repuestos',                     [InventarioController::class, 'repuestos'])->name('repuestos');
            Route::get('/repuestos/crear',               [InventarioController::class, 'crearRepuesto'])->name('repuesto.crear');
            Route::post('/repuestos',                    [InventarioController::class, 'guardarRepuesto'])->name('repuesto.guardar');
            Route::get('/repuestos/{repuesto}/editar',   [InventarioController::class, 'editarRepuesto'])->name('repuesto.editar');
            Route::put('/repuestos/{repuesto}',          [InventarioController::class, 'actualizarRepuesto'])->name('repuesto.actualizar');
            Route::post('/repuestos/{repuesto}/stock',   [InventarioController::class, 'ajustarStock'])->name('repuesto.stock');
            // Herramientas
            Route::get('/herramientas',                  [InventarioController::class, 'herramientas'])->name('herramientas');
            Route::post('/herramientas',                 [InventarioController::class, 'guardarHerramienta'])->name('herramienta.guardar');
        });

        // ── Proveedores (solo admin/administrativo) ───────────────
        Route::prefix('proveedores')
            ->name('proveedores.')
            ->middleware('role:admin,administrativo')
            ->group(function () {
                Route::get('/',                [ProveedorController::class, 'index'])->name('index');
                Route::get('/crear',           [ProveedorController::class, 'create'])->name('crear');
                Route::post('/',               [ProveedorController::class, 'store'])->name('guardar');
                Route::get('/{proveedor}/editar', [ProveedorController::class, 'edit'])->name('editar');
                Route::put('/{proveedor}',     [ProveedorController::class, 'update'])->name('actualizar');
                Route::delete('/{proveedor}',  [ProveedorController::class, 'destroy'])->name('eliminar');
            });

        // ── Reportes ─────────────────────────────────────────────
        Route::prefix('reportes')->name('reportes.')->group(function () {
            Route::get('/',                          [ReporteController::class, 'index'])->name('index');
            Route::get('/formulario',                [ReporteController::class, 'formulario'])->name('formulario');
            Route::get('/trabajos',                  [ReporteController::class, 'trabajosRealizados'])->name('trabajos');
            Route::get('/stock',                     [ReporteController::class, 'stockRepuestos'])->name('stock');
            Route::get('/turnos',                    [ReporteController::class, 'turnos'])->name('turnos');
        });

        // ── Usuarios (solo admin) ─────────────────────────────────
        Route::prefix('usuarios')
            ->name('usuarios.')
            ->middleware('role:admin')
            ->group(function () {
                Route::get('/',                [UsuarioController::class, 'index'])->name('index');
                Route::get('/crear',           [UsuarioController::class, 'create'])->name('crear');
                Route::post('/',               [UsuarioController::class, 'store'])->name('guardar');
                Route::get('/{usuario}/editar',[UsuarioController::class, 'edit'])->name('editar');
                Route::put('/{usuario}',       [UsuarioController::class, 'update'])->name('actualizar');
                Route::patch('/{usuario}/toggle', [UsuarioController::class, 'toggleActivo'])->name('toggle');
            });
    });

// ── Panel de CLIENTE ─────────────────────────────────────────────
Route::prefix('cliente')
    ->name('cliente.')
    ->middleware(['auth', 'role:cliente'])
    ->group(function () {

        Route::get('/dashboard', [ClienteDashboard::class, 'index'])->name('dashboard');

        // Turnos del cliente
        Route::prefix('turnos')->name('turnos.')->group(function () {
            Route::get('/',              [ClienteTurnoController::class, 'index'])->name('index');
            Route::get('/solicitar',     [ClienteTurnoController::class, 'solicitar'])->name('solicitar');
            Route::post('/solicitar',    [ClienteTurnoController::class, 'guardar'])->name('guardar');
            Route::get('/{turno}/confirmacion', [ClienteTurnoController::class, 'confirmacion'])->name('confirmacion');
            Route::post('/{turno}/cancelar',    [ClienteTurnoController::class, 'cancelar'])->name('cancelar');
        });

        // Vehículos del cliente
        Route::prefix('vehiculos')->name('vehiculos.')->group(function () {
            Route::get('/',          [VehiculoController::class, 'index'])->name('index');
            Route::get('/agregar',   [VehiculoController::class, 'create'])->name('crear');
            Route::post('/',         [VehiculoController::class, 'store'])->name('guardar');
        });

        // Consultar estado de reparación (pública pero también accesible desde el panel)
        Route::match(['get','post'], '/consultar-estado', [ClienteTurnoController::class, 'consultarEstado'])->name('consultar-estado');
    });

// ── Consulta de estado (pública, sin login) ──────────────────────
Route::match(['get','post'], '/consultar', [ClienteTurnoController::class, 'consultarEstado'])->name('consultar.estado');

// ── Solicitar turno sin login ─────────────────────────────────────
Route::get('/solicitar-turno', [ClienteTurnoController::class, 'solicitarPublico'])->name('turno.publico.solicitar');
Route::post('/solicitar-turno', [ClienteTurnoController::class, 'guardarPublico'])->name('turno.publico.guardar');
Route::get('/solicitar-turno/confirmacion/{numero}', [ClienteTurnoController::class, 'confirmacionPublica'])->name('turno.publico.confirmacion');

// ── API interna (AJAX) ───────────────────────────────────────────
Route::get('/api/marcas/{marca}/modelos', [ClienteTurnoController::class, 'modelosPorMarca'])->name('api.modelos');
