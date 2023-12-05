DROP DATABASE IF EXISTS ilerbank;
CREATE DATABASE ilerbank;
USE ilerbank;

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
	contrasena VARCHAR (255) NOT NULL
);



CREATE TABLE registro (
username VARCHAR (255) PRIMARY KEY,
contraseña VARCHAR (255)

);

CREATE TABLE IF NOT EXISTS movimientos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_usuario VARCHAR(255) NOT NULL,
    tipo_movimiento ENUM('ingreso', 'gasto') NOT NULL,
    monto DECIMAL(10, 2) NOT NULL,
    fecha_movimiento TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Agregar la restricción de clave foránea después de haber creado la tabla
ALTER TABLE movimientos
ADD CONSTRAINT fk_movimientos_usuarios
FOREIGN KEY (nombre_usuario) REFERENCES usuarios(nombre_usuario);


INSERT INTO usuarios (nombre_usuario, apellido, dni, email, fecha_nacimiento,direccion, codigo_postal, ciudad, provincia,contrasena) 
VALUES 
('lenny', 'Fdz', '53963457Y', 'niconefernandez@gmail.com', '1990-01-01', 'Sierra Nevada', '41120', 'Gelves', 'Sevilla','1234');





















































