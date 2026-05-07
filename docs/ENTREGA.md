# Entrega · Hotel Luna Azul

## URL local

`http://hotel-luna-azul.test/`

## Credenciales

| Email | Contraseña | Rol | Para qué sirve mostrar |
|---|---|---|---|
| `admin@lunaazul.test` | `LunaAzul2026!` | superadmin | Vista completa: usuarios + auditoría + reportes |
| `gerencia@lunaazul.test` | `Gerencia2026!` | admin | Mismo CRUD pero sin usuarios ni auditoría |
| `recepcion1@lunaazul.test` | `Recep2026!` | recepcionista | Solo opera reservas y huéspedes |
| `recepcion2@lunaazul.test` | `Recep2026!` | recepcionista | Segundo recepcionista (auditoría se ve quién hizo qué) |

## Mapeo a la rúbrica

| Criterio académico | Cómo se cumple en este sistema |
|---|---|
| **1. Capa de presentación clara y usable** | Diseño editorial (Fraunces + Plus Jakarta Sans), sidebar fijo, navegación por módulos, empty states por módulo, validaciones inline, CSS puro sin Bootstrap, AJAX sin recargas para autocomplete y disponibilidad. |
| **2. Modelo relacional + no-relacional** | 6 tablas normalizadas con FKs en `database/schema.sql` (roles, users, guests, rooms, reservations, permissions). **Capa no-relacional** en `storage/audit/YYYY-MM-DD.json` — un archivo JSON por día con append concurrente protegido por `flock`. Visible y filtrable desde el módulo de auditoría. |
| **3. Tres niveles de acceso** | Tabla `permissions` con matriz declarativa (módulo × acción × rol). `Auth::can($module, $action)` consulta esa tabla. `Auth::require()` aborta a 403 si no autoriza. La sidebar oculta los módulos sin permiso de `view`. |
| **4. Anti-doble-reserva** | Doble defensa: (a) `AvailabilityService::hasOverlap()` se ejecuta **antes** de cada INSERT/UPDATE; (b) endpoint `/api/availability` solo retorna habitaciones libres, así la UI ni siquiera muestra las ocupadas como opción. La consulta SQL usa `check_in < :new_check_out AND check_out > :new_check_in`. |
| **5. Reportes PDF** | Voucher de reserva (desde la ficha) y reporte de ocupación mensual (desde `/reports`). Generados con reportlab. Cada generación se loguea en auditoría con la entrada `report.generate`. |
| **6. Componente Python** | `python/generate_reservation_pdf.py` y `python/generate_occupancy_report.py`. Invocados vía `shell_exec` desde `App\Services\PdfService` con datos pasados en JSON. Dependencias en `python/requirements.txt`. |

## Capturas obligatorias para el video

1. **Login** (con campo de error visible al meter contraseña mala una vez).
2. **Dashboard** mostrando KPIs y la lista de "Llegadas/Salidas de hoy".
3. **Mapa de habitaciones** (vista en cuadrícula con badges).
4. **Flujo de nueva reserva**:
   - Empezar a escribir el nombre del huésped → autocomplete AJAX.
   - Escoger fechas → habitaciones libres aparecen como tiles seleccionables.
   - Total se calcula automáticamente.
5. **Demostración del anti-doble-reserva**:
   - Tomar una reserva existente (ej. `LA-2026-0007` que ocupa S-02 del 5 al 9 de mayo).
   - Intentar crear otra en S-02 con check_in 7 de mayo → sistema rechaza con mensaje rojo.
6. **Módulo de auditoría** (entrar como `admin@lunaazul.test`) → seleccionar el día actual, ver entradas, expandir el JSON.
7. **PDF generado**:
   - Voucher: ir a una reserva → botón "Voucher PDF".
   - Ocupación: `/reports` → seleccionar mes → "Generar PDF".

## Verificaciones rápidas (smoke test)

Probar con cada usuario:

- **superadmin** entra a TODOS los módulos.
- **admin** intenta entrar a `/users` o `/audit` → 403 ("Sin permiso para ver esto.").
- **recepcionista** intenta entrar a `/rooms/create` → 403. Igual con `/reports`.

## Datos del seed

- **3 roles**, **18 permisos** (matriz completa).
- **4 usuarios** (1 superadmin, 1 admin, 2 recepcionistas).
- **12 habitaciones** (6 estándar, 4 superior, 2 suite). Una en mantenimiento.
- **18 huéspedes** colombianos y extranjeros.
- **15 reservas** distribuidas: 4 finalizadas (abril), 2 canceladas, 3 en curso (mayo), 3 reservadas próximas, 3 futuras (junio).

"Hoy" del proyecto = **2026-05-07**. Las reservas están calibradas alrededor de esa fecha para que el dashboard muestre llegadas y salidas el día de hoy.

## Capa no-relacional (auditoría JSON)

Cada acción se loguea en `storage/audit/YYYY-MM-DD.json`. Ejemplo de una entrada:

```json
{
  "id": "F4A3...",
  "ts": "2026-05-07T08:14:22-05:00",
  "actor": { "id": 2, "name": "Carolina Mejía", "role": "superadmin" },
  "action": "reservation.create",
  "entity": { "type": "reservation", "id": 47, "code": "LA-2026-0047" },
  "ip": "127.0.0.1",
  "ua": "...",
  "data": { "guest_id": 12, "room_id": 4, "check_in": "2026-05-10", "check_out": "2026-05-13" }
}
```

Acciones registradas: `auth.login.success`, `auth.login.failed`, `auth.login.blocked`, `auth.logout`, `guest.create`, `guest.update`, `guest.delete`, `room.create`, `room.update`, `room.status_change`, `room.delete`, `reservation.create`, `reservation.update`, `reservation.cancel`, `reservation.check_in`, `reservation.check_out`, `user.create`, `user.update`, `user.deactivate`, `report.generate`.

## Estructura entregada

```
hotel-luna-azul/
├── PLAN.md
├── README.md
├── composer.json
├── .env, .env.example
├── .htaccess (raíz, redirige a /public)
│
├── app/
│   ├── Core/         (Database, Router, Auth, Csrf, Validator, View, Controller, Model, helpers, Autoload)
│   ├── Controllers/  (Auth, Dashboard, Guest, Room, Reservation, User, Audit, Report)
│   ├── Models/       (User, Guest, Room, Reservation)
│   ├── Services/     (AvailabilityService, AuditService, PdfService)
│   ├── Middleware/   (AuthMiddleware)
│   └── Views/        (layouts, partials, módulos, errores)
│
├── config/           (app.php, database.php, routes.php)
├── database/         (schema.sql, seed.sql)
├── docs/             (ENTREGA.md, INSTALACION.md)
├── public/           (index.php, .htaccess, assets/)
├── python/           (venv/, requirements.txt, scripts)
└── storage/          (audit/, reports/, logs/)
```

Total: ~50 archivos PHP, 5 hojas CSS, 4 archivos JS, 2 scripts Python.
