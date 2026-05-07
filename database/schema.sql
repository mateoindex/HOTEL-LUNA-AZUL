-- Hotel Luna Azul - schema relacional
-- ejecutar despues de crear la base con: CREATE DATABASE hotel_luna_azul CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS permissions;
DROP TABLE IF EXISTS reservations;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS roles;
DROP TABLE IF EXISTS rooms;
DROP TABLE IF EXISTS guests;

SET FOREIGN_KEY_CHECKS = 1;

-- ----------------------------------
-- roles
-- ----------------------------------
CREATE TABLE roles (
    id              INT PRIMARY KEY AUTO_INCREMENT,
    name            VARCHAR(40) UNIQUE NOT NULL,
    display_name    VARCHAR(60) NOT NULL,
    created_at      DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------
-- users
-- ----------------------------------
CREATE TABLE users (
    id              INT PRIMARY KEY AUTO_INCREMENT,
    role_id         INT NOT NULL,
    full_name       VARCHAR(120) NOT NULL,
    email           VARCHAR(120) UNIQUE NOT NULL,
    password_hash   VARCHAR(255) NOT NULL,
    is_active       TINYINT(1) NOT NULL DEFAULT 1,
    last_login_at   DATETIME NULL,
    created_at      DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_users_role FOREIGN KEY (role_id) REFERENCES roles(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------
-- guests (huespedes)
-- ----------------------------------
CREATE TABLE guests (
    id              INT PRIMARY KEY AUTO_INCREMENT,
    document_type   ENUM('CC','CE','PAS') NOT NULL,
    document_number VARCHAR(30) UNIQUE NOT NULL,
    first_name      VARCHAR(80) NOT NULL,
    last_name       VARCHAR(80) NOT NULL,
    email           VARCHAR(120) NULL,
    phone           VARCHAR(30) NULL,
    country         VARCHAR(60) NOT NULL DEFAULT 'Colombia',
    city            VARCHAR(80) NULL,
    notes           TEXT NULL,
    created_at      DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE INDEX idx_guests_name ON guests(last_name, first_name);

-- ----------------------------------
-- rooms (habitaciones)
-- ----------------------------------
CREATE TABLE rooms (
    id              INT PRIMARY KEY AUTO_INCREMENT,
    code            VARCHAR(10) UNIQUE NOT NULL,
    type            ENUM('estandar','superior','suite') NOT NULL,
    capacity        TINYINT NOT NULL,
    price_per_night DECIMAL(10,2) NOT NULL,
    floor           TINYINT NOT NULL,
    status          ENUM('disponible','mantenimiento','fuera_servicio') NOT NULL DEFAULT 'disponible',
    description     TEXT NULL,
    created_at      DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------
-- reservations
-- ----------------------------------
CREATE TABLE reservations (
    id              INT PRIMARY KEY AUTO_INCREMENT,
    code            VARCHAR(20) UNIQUE NOT NULL,
    guest_id        INT NOT NULL,
    room_id         INT NOT NULL,
    created_by      INT NOT NULL,
    check_in        DATE NOT NULL,
    check_out       DATE NOT NULL,
    nights          INT GENERATED ALWAYS AS (DATEDIFF(check_out, check_in)) STORED,
    adults          TINYINT NOT NULL DEFAULT 1,
    children        TINYINT NOT NULL DEFAULT 0,
    total_amount    DECIMAL(12,2) NOT NULL,
    status          ENUM('reservada','en_curso','finalizada','cancelada') NOT NULL DEFAULT 'reservada',
    notes           TEXT NULL,
    created_at      DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_res_guest FOREIGN KEY (guest_id) REFERENCES guests(id),
    CONSTRAINT fk_res_room  FOREIGN KEY (room_id)  REFERENCES rooms(id),
    CONSTRAINT fk_res_user  FOREIGN KEY (created_by) REFERENCES users(id),
    CONSTRAINT chk_res_dates CHECK (check_out > check_in)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE INDEX idx_room_dates ON reservations(room_id, check_in, check_out, status);
CREATE INDEX idx_res_status ON reservations(status);

-- ----------------------------------
-- permissions (matriz declarativa)
-- ----------------------------------
CREATE TABLE permissions (
    id              INT PRIMARY KEY AUTO_INCREMENT,
    role_id         INT NOT NULL,
    module          VARCHAR(40) NOT NULL,
    can_view        TINYINT(1) NOT NULL DEFAULT 0,
    can_create      TINYINT(1) NOT NULL DEFAULT 0,
    can_edit        TINYINT(1) NOT NULL DEFAULT 0,
    can_delete      TINYINT(1) NOT NULL DEFAULT 0,
    UNIQUE KEY uk_role_module (role_id, module),
    CONSTRAINT fk_perm_role FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
