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

-- Tabla de usuarioAdmin
CREATE TABLE usuarioAdmin (
    username VARCHAR(255) PRIMARY KEY,
    contraseña VARCHAR(255)
);

-- Insertar un usuario administrador de ejemplo
INSERT INTO usuarioAdmin (username, contraseña)
VALUES ('admin', 'admin123');

-- Insertar un usuario normal de ejemplo
INSERT INTO usuarios (nombre_usuario, apellido, dni, email, fecha_nacimiento, direccion, codigo_postal, ciudad, provincia, contrasena) 
VALUES ('lenny', 'Fdz', '53963457Y', 'niconefernandez@gmail.com', '1990-01-01', 'Sierra Nevada', '41120', 'Gelves', 'Sevilla', '1234');
