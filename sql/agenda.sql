-- Instalación sin borrar datos existentes. No modifica tablas ya creadas.
CREATE DATABASE IF NOT EXISTS agenda_personal
CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE agenda_personal;
CREATE TABLE IF NOT EXISTS usuarios (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    correo VARCHAR(254) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    fecha_registro TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS contactos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT UNSIGNED NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    telefono VARCHAR(25) NOT NULL,
    correo VARCHAR(254) NOT NULL,
    ciudad VARCHAR(100) NOT NULL,
    CONSTRAINT fk_contactos_usuario FOREIGN KEY (id_usuario)
        REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB;
