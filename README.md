# 🔧 Taller Aquino — Sistema de Gestión ERP

Sistema de gestión integral para talleres mecánicos desarrollado con Laravel 11 y MySQL. Cubre todo el ciclo operativo del taller: desde la solicitud de turnos hasta la facturación, contabilidad y gestión de recursos humanos.

---

## 📋 Descripción

Taller Aquino es un sistema ERP (Enterprise Resource Planning) diseñado para digitalizar y optimizar la operación de un taller mecánico. Permite gestionar turnos, órdenes de trabajo, inventario, facturación, compras a proveedores, contabilidad básica y recursos humanos desde una sola plataforma, con roles diferenciados para administradores, mecánicos y clientes.

---

## 🚀 Tecnologías utilizadas

- **Backend:** PHP 8.3 · Laravel 11
- **Base de datos:** MySQL (Laragon)
- **Frontend:** Blade Templates · Bootstrap Icons · CSS Variables
- **Tipografía:** Google Fonts (Oswald + Source Sans 3)
- **Gráficos:** Chart.js
- **Control de versiones:** Git · GitHub
- **Entorno local:** Laragon

---

## ✨ Funcionalidades principales

### 👥 Sistema de roles
El sistema maneja 4 roles con accesos diferenciados:
- **Admin** — acceso total a todos los módulos
- **Administrativo** — operaciones, inventario y finanzas
- **Mecánico** — órdenes de trabajo e inventario
- **Cliente** — portal propio con turnos, vehículos y facturas

---

### 📅 Módulo de Turnos
- Solicitud de turnos online desde el portal del cliente
- Solicitud de turnos sin cuenta (modo invitado)
- Registro de turnos presenciales desde el panel admin
- Calendario visual con horarios disponibles
- Confirmación de turno con asignación de mecánico (ordenados por carga de trabajo)
- Número de seguimiento único por turno
- Cancelación con límite de 2 por mes y suspensión automática
- Agenda visual para administradores
- Filtros por estado y fecha

---

### 🔧 Módulo de Órdenes de Trabajo
- Registro de ingreso de vehículos al taller desde turnos confirmados
- Registro de trabajos realizados con tipo de servicio, descripción y costos
- Gestión de repuestos utilizados con descuento automático de stock
- Estados del vehículo: ingresado → en proceso → finalizado → entregado
- Registro de egreso con firma de conformidad del cliente
- Filtros por estado incluyendo vehículos entregados

---

### 📦 Módulo de Inventario
- Alta, edición y control de repuestos
- Control de stock con alertas de stock bajo
- Ajuste de stock manual
- Integración con órdenes de trabajo (descuento automático al registrar trabajos)
- Gestión de herramientas

---

### 💰 Módulo de Facturación y Caja
- Generación de facturas y presupuestos desde órdenes finalizadas
- Precios de venta manuales (mano de obra + repuestos + descuento)
- Numeración correlativa automática (FAC-00001)
- Registro de pagos por múltiples métodos (efectivo, transferencia, tarjeta, cheque)
- Estados de factura: pendiente, pago parcial, pagada, anulada
- Caja diaria con ingresos, egresos y saldo
- Registro de movimientos manuales de caja (gastos, sueldos, servicios)
- Selector de fecha para consultar cualquier día

---

### 🛒 Módulo de Compras y Proveedores
- Gestión de proveedores (alta, edición, estado activo/inactivo)
- Creación de órdenes de compra con múltiples repuestos
- Numeración correlativa automática (OC-00001)
- Recepción parcial o total de mercadería
- Actualización automática de stock al recibir mercadería
- Actualización automática del costo del repuesto
- Registro automático del egreso en caja al recibir
- Estados: enviada, recibida parcial, recibida, cancelada

---

### 📊 Módulo de Contabilidad
- **Libro de ingresos y egresos** — movimientos del período con filtro por mes y año
- **Rentabilidad mensual** — tabla anual con ingresos, egresos, ganancia y margen por mes con gráfico de barras (Chart.js)
- **Margen por trabajo** — comparación costo vs precio de venta por factura con filtro por rango de fechas
- KPIs visuales en todos los apartados

---

### 👷 Módulo de RRHH
- Panel con KPIs del equipo de mecánicos
- Perfil individual con filtro por mes y año
- Registro de horas trabajadas (normales y extra)
- Comisiones por trabajo sobre mano de obra con porcentaje configurable
- Pago de comisiones con registro automático en caja como egreso
- Historial completo por mecánico

---

### 🧾 Portal del Cliente
- Dashboard con resumen de turnos activos
- Solicitud de turnos con calendario visual y selección de horario
- Marca y modelo personalizado ("Otra" opción)
- Seguimiento del estado de reparación por número de seguimiento
- Historial de vehículos registrados
- **Mis Facturas** — vista de facturas con estado de pago y saldo pendiente
- Consulta de estado sin necesidad de cuenta

---

## 🗂️ Estructura de la base de datos

El sistema cuenta con las siguientes tablas principales:

`users` · `vehiculos` · `marcas` · `modelos` · `turnos` · `ingresos_vehiculo` · `trabajos_realizados` · `repuestos` · `herramientas` · `proveedores` · `ordenes_compra` · `ordenes_compra_items` · `recepciones_compra` · `facturas` · `pagos` · `movimientos_caja` · `horas_trabajo` · `comisiones`

---

## ⚙️ Instalación local

### Requisitos previos
- PHP 8.3+
- MySQL 8+
- Composer
- Laragon (recomendado) o cualquier servidor local

### Pasos

```bash
# 1. Clonar el repositorio
git clone https://github.com/Gonzaaa4/taller-aquino.git
cd taller-aquino

# 2. Instalar dependencias
composer install

# 3. Configurar el entorno
cp .env.example .env
php artisan key:generate

# 4. Configurar la base de datos en .env
DB_DATABASE=taller_aquino
DB_USERNAME=root
DB_PASSWORD=

# 5. Ejecutar migraciones y seeders
php artisan migrate --seed

# 6. Iniciar el servidor
php artisan serve
```

### Acceso al sistema

| Rol | Email | Contraseña |
|-----|-------|------------|
| Admin | admin@talleraquino.com | Admin1234! |
| Administrativo | administrativa@talleraquino.com | Admin1234! |
| Mecánico | mecanico1@talleraquino.com | Admin1234! |
| Cliente | cliente@ejemplo.com | Cliente1234! |

---

## 📁 Estructura del proyecto

```
app/
├── Http/Controllers/
│   ├── Admin/          # Controladores del panel admin
│   └── Cliente/        # Controladores del portal cliente
├── Models/             # Modelos Eloquent
resources/
├── views/
│   ├── admin/          # Vistas del panel admin
│   ├── cliente/        # Vistas del portal cliente
│   ├── auth/           # Login y registro
│   └── layouts/        # Layout principal
routes/
└── web.php             # Todas las rutas del sistema
database/
└── migrations/         # Migraciones de la base de datos
```

---

## 🔐 Seguridad

- Autenticación propia con hash de contraseñas (bcrypt)
- Middleware de roles para proteger rutas por perfil
- Verificación de pertenencia en recursos del cliente (facturas, turnos, vehículos)
- Tokens CSRF en todos los formularios
- Validación server-side en todos los endpoints

---

## 👨‍💻 Autor

Desarrollado por **Gonzalo Aquino** como proyecto personal de portfolio.

- GitHub: [@Gonzaaa4](https://github.com/Gonzaaa4)

---

## 📄 Licencia

Este proyecto es de uso personal y educativo.
