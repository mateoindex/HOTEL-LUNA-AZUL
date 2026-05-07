-- Hotel Luna Azul - datos iniciales
-- corre despues de schema.sql
-- "hoy" del proyecto = 2026-05-07

USE hotel_luna_azul;

-- ----------------------------------
-- roles
-- ----------------------------------
INSERT INTO roles (id, name, display_name) VALUES
    (1, 'superadmin',    'Super administrador'),
    (2, 'admin',         'Administrador'),
    (3, 'recepcionista', 'Recepcionista');

-- ----------------------------------
-- permissions (matriz de la seccion 8.2)
-- ----------------------------------
INSERT INTO permissions (role_id, module, can_view, can_create, can_edit, can_delete) VALUES
    -- superadmin: todo
    (1, 'guests',       1,1,1,1),
    (1, 'rooms',        1,1,1,1),
    (1, 'reservations', 1,1,1,1),
    (1, 'users',        1,1,1,1),
    (1, 'audit',        1,0,0,0),
    (1, 'reports',      1,1,0,0),
    -- admin
    (2, 'guests',       1,1,1,1),
    (2, 'rooms',        1,1,1,0),
    (2, 'reservations', 1,1,1,1),
    (2, 'users',        0,0,0,0),
    (2, 'audit',        0,0,0,0),
    (2, 'reports',      1,1,0,0),
    -- recepcionista
    (3, 'guests',       1,1,1,0),
    (3, 'rooms',        1,0,0,0),
    (3, 'reservations', 1,1,1,0),
    (3, 'users',        0,0,0,0),
    (3, 'audit',        0,0,0,0),
    (3, 'reports',      0,0,0,0);

-- ----------------------------------
-- users (hashes bcrypt precomputados)
-- LunaAzul2026!  / Gerencia2026!  / Recep2026!
-- ----------------------------------
INSERT INTO users (id, role_id, full_name, email, password_hash, is_active) VALUES
    (1, 1, 'Carolina Mejía',  'admin@lunaazul.test',       '$2y$10$q6whLZ01vZ0AqLbqDqn3xuQ8GFLs3hPTWSpxZ2TZ5x5PbVku9Lo76', 1),
    (2, 2, 'Andrés Torres',   'gerencia@lunaazul.test',    '$2y$10$XCk/ElpnzWMNQZErSsvz2.lPYhDkQ4jygi5WZ8.Ii.Q7aDcwGzbne', 1),
    (3, 3, 'Daniela Ríos',    'recepcion1@lunaazul.test',  '$2y$10$L1yukWfORDhxbfggXZEvZeHcLK3KFXiUiafEy3Coao4TAjozi6gne', 1),
    (4, 3, 'Mateo Vargas',    'recepcion2@lunaazul.test',  '$2y$10$L1yukWfORDhxbfggXZEvZeHcLK3KFXiUiafEy3Coao4TAjozi6gne', 1);

-- ----------------------------------
-- rooms (12 llaves)
-- ----------------------------------
INSERT INTO rooms (id, code, type, capacity, price_per_night, floor, status, description) VALUES
    -- estandar (piso 1)
    (1,  '101', 'estandar', 2, 320000.00, 1, 'disponible',     'Habitación estándar con cama queen, vista interior al patio.'),
    (2,  '102', 'estandar', 2, 320000.00, 1, 'disponible',     'Habitación estándar, dos camas individuales.'),
    (3,  '103', 'estandar', 2, 320000.00, 1, 'disponible',     'Habitación estándar con cama queen.'),
    (4,  '104', 'estandar', 2, 320000.00, 1, 'mantenimiento',  'En revisión de aire acondicionado.'),
    (5,  '105', 'estandar', 2, 320000.00, 1, 'disponible',     'Habitación estándar con balcón pequeño.'),
    (6,  '106', 'estandar', 2, 320000.00, 1, 'disponible',     'Habitación estándar, esquinera.'),
    -- superior (piso 2)
    (7,  '201', 'superior', 3, 480000.00, 2, 'disponible',     'Superior con cama king y sofá cama, vista al mar parcial.'),
    (8,  '202', 'superior', 2, 480000.00, 2, 'disponible',     'Superior con cama king, vista al patio.'),
    (9,  '203', 'superior', 3, 480000.00, 2, 'disponible',     'Superior con cama king y sofá cama.'),
    (10, '204', 'superior', 2, 480000.00, 2, 'disponible',     'Superior esquinera con balcón amplio.'),
    -- suites (piso 3)
    (11, 'S-01','suite',    4, 890000.00, 3, 'disponible',     'Suite con sala independiente, dos ambientes, vista al mar.'),
    (12, 'S-02','suite',    4, 890000.00, 3, 'disponible',     'Suite presidencial con jacuzzi y terraza privada.');

-- ----------------------------------
-- guests (18)
-- ----------------------------------
INSERT INTO guests (id, document_type, document_number, first_name, last_name, email, phone, country, city, notes) VALUES
    (1,  'CC',  '1020345678', 'Laura',     'Castaño',     'laura.castano@correo.co',   '+57 310 555 1122', 'Colombia',     'Medellín',     'Solicita habitación piso alto.'),
    (2,  'CC',  '1098765432', 'Sebastián', 'Quintero',    'squintero@correo.co',       '+57 311 444 8899', 'Colombia',     'Bogotá',       NULL),
    (3,  'CC',  '52765432',   'Marcela',   'Hoyos',       'marcela.h@correo.co',       '+57 320 333 2211', 'Colombia',     'Cali',         'Alérgica a mariscos.'),
    (4,  'PAS', 'AB-998877',  'James',     'Wilson',      'jwilson@example.com',       '+1 305 555 0199',  'Estados Unidos','Miami',       NULL),
    (5,  'PAS', 'X1122334',   'Sofía',     'García',      'sgarcia@correo.es',         '+34 612 998 776',  'España',       'Madrid',       'Llega tarde, dejar llaves en recepción.'),
    (6,  'CE',  'CE-554433',  'Lucía',     'Fernández',   'lucia.f@correo.com',        '+54 911 444 7788', 'Argentina',    'Buenos Aires', NULL),
    (7,  'CC',  '1140998877', 'Camilo',    'Restrepo',    'camilor@correo.co',         '+57 312 998 4455', 'Colombia',     'Cartagena',    'Cliente frecuente.'),
    (8,  'CC',  '1019887766', 'Valentina', 'Ospina',      'vale.ospina@correo.co',     '+57 314 887 6655', 'Colombia',     'Medellín',     NULL),
    (9,  'PAS', 'MX-887766',  'Diego',     'Hernández',   'dhernandez@correo.mx',      '+52 55 1234 5678', 'México',       'CDMX',         'Aniversario de bodas.'),
    (10, 'CC',  '79554433',   'Ricardo',   'Pulido',      'rpulido@correo.co',         '+57 313 221 9988', 'Colombia',     'Bogotá',       NULL),
    (11, 'CC',  '1066778899', 'Andrea',    'Salazar',     'asalazar@correo.co',        '+57 318 776 3344', 'Colombia',     'Barranquilla', NULL),
    (12, 'PAS', 'PE-334455',  'Gabriela',  'Ramos',       'gramos@correo.pe',          '+51 999 887 665',  'Perú',         'Lima',         'Vegetariana.'),
    (13, 'CC',  '1143556677', 'Felipe',    'Arango',      'farango@correo.co',         '+57 315 998 2233', 'Colombia',     'Pereira',      NULL),
    (14, 'CE',  'CE-998811',  'Mateo',     'Suárez',      'msuarez@correo.com',        '+56 9 8877 4455',  'Chile',        'Santiago',     NULL),
    (15, 'CC',  '52998877',   'Paula',     'Gómez',       'paulagomez@correo.co',      '+57 311 654 8899', 'Colombia',     'Bogotá',       'Pidió cuna para bebé.'),
    (16, 'PAS', 'US-554477',  'Emily',     'Anderson',    'eanderson@example.com',     '+1 415 555 7788',  'Estados Unidos','San Francisco', NULL),
    (17, 'CC',  '1112334455', 'Juan',      'Pérez',       'jperez@correo.co',          '+57 319 332 1144', 'Colombia',     'Cartagena',    NULL),
    (18, 'CC',  '1067554433', 'Isabella',  'Mendoza',     'imendoza@correo.co',        '+57 316 778 9911', 'Colombia',     'Santa Marta',  NULL);

-- ----------------------------------
-- reservations (15) - mezcla alrededor de 2026-05-07
-- ----------------------------------
INSERT INTO reservations (code, guest_id, room_id, created_by, check_in, check_out, adults, children, total_amount, status, notes) VALUES
    -- finalizadas en abril
    ('LA-2026-0001', 1,  1, 3, '2026-04-02', '2026-04-05', 2, 0,  960000.00, 'finalizada', NULL),
    ('LA-2026-0002', 4,  7, 3, '2026-04-08', '2026-04-12', 2, 0, 1920000.00, 'finalizada', 'Pago con tarjeta extranjera.'),
    ('LA-2026-0003', 3,  2, 4, '2026-04-15', '2026-04-18', 1, 0,  960000.00, 'finalizada', NULL),
    ('LA-2026-0004', 9, 11, 3, '2026-04-20', '2026-04-25', 2, 0, 4450000.00, 'finalizada', 'Aniversario, decoración floral.'),

    -- canceladas
    ('LA-2026-0005', 5,  3, 4, '2026-04-30', '2026-05-02', 1, 0,  640000.00, 'cancelada',  'Canceló por vuelo perdido.'),
    ('LA-2026-0006', 8,  9, 3, '2026-05-12', '2026-05-15', 3, 0, 1440000.00, 'cancelada',  NULL),

    -- en curso (entrada anterior, salida posterior a hoy 2026-05-07)
    ('LA-2026-0007', 7, 12, 3, '2026-05-05', '2026-05-09', 2, 1, 3560000.00, 'en_curso',   'Cliente frecuente, upgrade gratis.'),
    ('LA-2026-0008', 2,  5, 4, '2026-05-06', '2026-05-08', 2, 0,  640000.00, 'en_curso',   NULL),

    -- llegadas hoy (check_in = 2026-05-07)
    ('LA-2026-0009', 6,  8, 3, '2026-05-07', '2026-05-10', 2, 0, 1440000.00, 'reservada',  NULL),
    ('LA-2026-0010', 11, 6, 3, '2026-05-07', '2026-05-09', 2, 0,  640000.00, 'reservada',  'Pidió toallas extra.'),

    -- salidas hoy (check_out = 2026-05-07)
    ('LA-2026-0011', 10, 3, 4, '2026-05-04', '2026-05-07', 1, 0,  960000.00, 'en_curso',   'Sale temprano.'),

    -- proximas reservadas
    ('LA-2026-0012', 12, 10, 3, '2026-05-15', '2026-05-19', 2, 0, 1920000.00, 'reservada', 'Vegetariana, avisar restaurante.'),
    ('LA-2026-0013', 14,  7, 3, '2026-05-22', '2026-05-25', 2, 0, 1440000.00, 'reservada', NULL),

    -- futuras junio
    ('LA-2026-0014', 15,  1, 4, '2026-06-03', '2026-06-07', 2, 1, 1280000.00, 'reservada', 'Cuna para bebé.'),
    ('LA-2026-0015', 16, 11, 3, '2026-06-10', '2026-06-15', 2, 0, 4450000.00, 'reservada', 'Luna de miel.');
