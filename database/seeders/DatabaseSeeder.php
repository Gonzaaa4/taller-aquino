<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Marca;
use App\Models\Modelo;
use App\Models\Proveedor;
use App\Models\Repuesto;
use App\Models\Herramienta;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Usuarios iniciales ───────────────────────────────────
        User::create([
            'name'     => 'Gonzalo',
            'apellido' => 'Aquino',
            'email'    => 'admin@talleraquino.com',
            'dni'      => '30000001',
            'telefono' => '3751-000001',
            'rol'      => 'admin',
            'password' => Hash::make('Admin1234!'),
            'activo'   => true,
        ]);

        User::create([
            'name'     => 'María',
            'apellido' => 'González',
            'email'    => 'administrativa@talleraquino.com',
            'dni'      => '30000002',
            'telefono' => '3751-000002',
            'rol'      => 'administrativo',
            'password' => Hash::make('Admin1234!'),
            'activo'   => true,
        ]);

        User::create([
            'name'     => 'Carlos',
            'apellido' => 'Rodríguez',
            'email'    => 'mecanico1@talleraquino.com',
            'dni'      => '30000003',
            'telefono' => '3751-000003',
            'rol'      => 'mecanico',
            'password' => Hash::make('Admin1234!'),
            'activo'   => true,
        ]);

        User::create([
            'name'     => 'Lucas',
            'apellido' => 'Fernández',
            'email'    => 'mecanico2@talleraquino.com',
            'dni'      => '30000004',
            'telefono' => '3751-000004',
            'rol'      => 'mecanico',
            'password' => Hash::make('Admin1234!'),
            'activo'   => true,
        ]);

        // Cliente de prueba
        User::create([
            'name'     => 'Juan',
            'apellido' => 'Pérez',
            'email'    => 'cliente@ejemplo.com',
            'dni'      => '35000001',
            'telefono' => '3751-111111',
            'rol'      => 'cliente',
            'password' => Hash::make('Cliente1234!'),
            'activo'   => true,
        ]);

        // ── Marcas y modelos ─────────────────────────────────────
        $marcasData = [
            'Toyota'      => ['Corolla', 'Hilux', 'RAV4', 'Etios', 'Yaris', 'Camry'],
            'Ford'        => ['Focus', 'Ranger', 'Fiesta', 'EcoSport', 'Ka', 'Maverick'],
            'Chevrolet'   => ['Cruze', 'S10', 'Onix', 'Spin', 'Tracker', 'Agile'],
            'Volkswagen'  => ['Gol', 'Polo', 'Amarok', 'Vento', 'Tiguan', 'T-Cross'],
            'Renault'     => ['Sandero', 'Logan', 'Duster', 'Kwid', 'Kangoo', 'Megane'],
            'Peugeot'     => ['208', '308', '408', '2008', '3008', 'Partner'],
            'Fiat'        => ['Palio', 'Cronos', 'Mobi', 'Toro', 'Strada', 'Pulse'],
            'Honda'       => ['Civic', 'HR-V', 'City', 'Fit', 'CR-V', 'WR-V'],
            'Nissan'      => ['Frontier', 'Sentra', 'Kicks', 'Versa', 'March'],
            'Hyundai'     => ['HB20', 'Tucson', 'Creta', 'Elantra', 'ix35'],
        ];

        foreach ($marcasData as $nombreMarca => $modelos) {
            $marca = Marca::create(['nombre' => $nombreMarca]);
            foreach ($modelos as $nombreModelo) {
                Modelo::create(['nombre' => $nombreModelo, 'marca_id' => $marca->id]);
            }
        }

        // ── Proveedores ──────────────────────────────────────────
        $prov1 = Proveedor::create([
            'nombre'    => 'AutoRepuestos Montecarlo',
            'telefono'  => '3758-123456',
            'email'     => 'ventas@autorepuestos-montecarlo.com',
            'direccion' => 'Av. San Martín 450, Montecarlo, Misiones',
            'categoria' => 'repuestos',
        ]);

        $prov2 = Proveedor::create([
            'nombre'    => 'Lubricentro Eldorado',
            'telefono'  => '3751-654321',
            'email'     => 'info@lubricentro-eldorado.com',
            'direccion' => 'Ruta 12 km 3, Eldorado, Misiones',
            'categoria' => 'lubricantes',
        ]);

        $prov3 = Proveedor::create([
            'nombre'    => 'Herramientas del Norte',
            'telefono'  => '3751-789456',
            'email'     => 'contacto@herramientasdelnorte.com',
            'direccion' => 'Eldorado, Misiones',
            'categoria' => 'herramientas',
        ]);

        // ── Repuestos iniciales ──────────────────────────────────
        $repuestos = [
            ['nombre' => 'Filtro de aceite universal',        'categoria' => 'filtros',      'cantidad_stock' => 20, 'stock_minimo' => 5,  'costo' => 1500,  'proveedor_id' => $prov1->id],
            ['nombre' => 'Filtro de aire universal',          'categoria' => 'filtros',      'cantidad_stock' => 15, 'stock_minimo' => 4,  'costo' => 2200,  'proveedor_id' => $prov1->id],
            ['nombre' => 'Aceite de motor 10W40 (1L)',        'categoria' => 'lubricantes',  'cantidad_stock' => 50, 'stock_minimo' => 10, 'costo' => 3500,  'proveedor_id' => $prov2->id],
            ['nombre' => 'Aceite de motor 15W40 (1L)',        'categoria' => 'lubricantes',  'cantidad_stock' => 30, 'stock_minimo' => 10, 'costo' => 3200,  'proveedor_id' => $prov2->id],
            ['nombre' => 'Pastillas de freno delanteras',     'categoria' => 'frenos',       'cantidad_stock' => 8,  'stock_minimo' => 2,  'costo' => 8500,  'proveedor_id' => $prov1->id],
            ['nombre' => 'Pastillas de freno traseras',       'categoria' => 'frenos',       'cantidad_stock' => 6,  'stock_minimo' => 2,  'costo' => 7200,  'proveedor_id' => $prov1->id],
            ['nombre' => 'Disco de freno delantero',          'categoria' => 'frenos',       'cantidad_stock' => 4,  'stock_minimo' => 2,  'costo' => 15000, 'proveedor_id' => $prov1->id],
            ['nombre' => 'Bujías (juego x4)',                 'categoria' => 'motor',        'cantidad_stock' => 10, 'stock_minimo' => 3,  'costo' => 6500,  'proveedor_id' => $prov1->id],
            ['nombre' => 'Correa de distribución',            'categoria' => 'motor',        'cantidad_stock' => 3,  'stock_minimo' => 1,  'costo' => 12000, 'proveedor_id' => $prov1->id],
            ['nombre' => 'Bomba de agua',                     'categoria' => 'motor',        'cantidad_stock' => 2,  'stock_minimo' => 1,  'costo' => 18000, 'proveedor_id' => $prov1->id],
            ['nombre' => 'Amortiguador delantero',            'categoria' => 'suspension',   'cantidad_stock' => 4,  'stock_minimo' => 2,  'costo' => 22000, 'proveedor_id' => $prov1->id],
            ['nombre' => 'Líquido de frenos DOT4 (500ml)',    'categoria' => 'frenos',       'cantidad_stock' => 12, 'stock_minimo' => 3,  'costo' => 2800,  'proveedor_id' => $prov2->id],
            ['nombre' => 'Batería 12V 60Ah',                  'categoria' => 'electrico',    'cantidad_stock' => 3,  'stock_minimo' => 1,  'costo' => 45000, 'proveedor_id' => $prov1->id],
            ['nombre' => 'Filtro de combustible',             'categoria' => 'filtros',      'cantidad_stock' => 8,  'stock_minimo' => 2,  'costo' => 3500,  'proveedor_id' => $prov1->id],
            ['nombre' => 'Aceite de caja manual (1L)',        'categoria' => 'transmision',  'cantidad_stock' => 10, 'stock_minimo' => 3,  'costo' => 4200,  'proveedor_id' => $prov2->id],
        ];

        foreach ($repuestos as $datos) {
            Repuesto::create($datos);
        }

        // ── Herramientas ─────────────────────────────────────────
        $herramientas = [
            ['nombre' => 'Llave torquímetro',      'tipo' => 'manual',      'estado' => 'disponible'],
            ['nombre' => 'Scanner OBD2 profesional','tipo' => 'electrica', 'estado' => 'disponible'],
            ['nombre' => 'Elevador hidráulico',    'tipo' => 'especializada','estado' => 'disponible'],
            ['nombre' => 'Gato hidráulico 3T',     'tipo' => 'manual',      'estado' => 'disponible'],
            ['nombre' => 'Juego de llaves combinadas', 'tipo' => 'manual',  'estado' => 'disponible'],
            ['nombre' => 'Soldadora MIG',          'tipo' => 'electrica',   'estado' => 'disponible'],
            ['nombre' => 'Compresor de aire 50L',  'tipo' => 'electrica',   'estado' => 'disponible'],
            ['nombre' => 'Multímetro digital',     'tipo' => 'medicion',    'estado' => 'disponible'],
            ['nombre' => 'Prensa hidráulica',      'tipo' => 'especializada','estado' => 'disponible'],
            ['nombre' => 'Alineadora de dirección','tipo' => 'especializada','estado' => 'disponible'],
        ];

        foreach ($herramientas as $h) {
            Herramienta::create($h);
        }
    }
}
