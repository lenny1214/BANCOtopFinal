-- Crear la base de datos
DROP DATABASE IF EXISTS ilerbank;
CREATE DATABASE ilerbank;
USE ilerbank;

-- Tabla de usuarios
CREATE TABLE usuarios (
    nombre_usuario VARCHAR(255) NOT NULL PRIMARY KEY,
    apellido VARCHAR(255) NOT NULL,
    dni VARCHAR(20) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    fecha_nacimiento DATE,
    direccion VARCHAR(255),
    codigo_postal VARCHAR(10),
    ciudad VARCHAR(255),
    provincia VARCHAR(255),
    contrasena VARCHAR(255) NOT NULL,
    iban VARCHAR(34)
);

-- Tabla de registro (asumo que se trata de usuarios normales)
CREATE TABLE registro (
    username VARCHAR(255) PRIMARY KEY,
    contraseña VARCHAR(255)
);

-- Tabla de movimientos
CREATE TABLE movimientos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_usuario VARCHAR(255) NOT NULL,
    tipo_movimiento ENUM('ingreso', 'gasto') NOT NULL,
    monto DECIMAL(10, 2) NOT NULL,
    fecha_movimiento TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (nombre_usuario) REFERENCES usuarios(nombre_usuario)
);

CREATE TABLE prestamos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_usuario VARCHAR(255) NOT NULL,
    cantidad DECIMAL(10, 2) NOT NULL,
    concepto VARCHAR(255) NOT NULL,
    amortizacion_meses INT NOT NULL,
    cuota_mensual DECIMAL(10, 2) NOT NULL,
    fecha_solicitud TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (nombre_usuario) REFERENCES usuarios(nombre_usuario)
);


ALTER TABLE usuarios
ADD COLUMN es_administrador BOOLEAN NOT NULL DEFAULT 0;


-- Actualizar el usuario 'admin' para que sea administrador
UPDATE usuarios 
SET 
    es_administrador = 1
WHERE
    nombre_usuario = 'admin';

-- Añadir columna de saldo a la tabla de usuarios
ALTER TABLE usuarios
ADD COLUMN saldo DECIMAL(10, 2) NOT NULL DEFAULT 0;

ALTER TABLE usuarios
ADD COLUMN foto_perfil VARCHAR(255) DEFAULT 'img/user.jpg';


INSERT INTO usuarios (nombre_usuario, apellido, dni, email, fecha_nacimiento, direccion, codigo_postal, ciudad, provincia, contrasena, iban, es_administrador)
VALUES ('admin', 'Admin', '12345678A', 'admin@example.com', '1980-01-01', 'Admin Street', '12345', 'Admin City', 'Admin Province', 'admin123', 'ES0123456789012345678901', 1);


-- Insertar un usuario normal de ejemplo
INSERT INTO usuarios (nombre_usuario, apellido, dni, email, fecha_nacimiento, direccion, codigo_postal, ciudad, provincia, contrasena) 
VALUES ('lenny', 'Fdz', '53963457Y', 'niconefernandez@gmail.com', '1990-01-01', 'Sierra Nevada', '41120', 'Gelves', 'Sevilla', '1234');
