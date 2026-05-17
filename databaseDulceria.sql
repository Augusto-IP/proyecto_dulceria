-- 1. Crear y usar la Base de Datos
CREATE DATABASE IF NOT EXISTS pasteleria_sistema_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE pasteleria_sistema_db;

DROP TABLE IF EXISTS personal;
CREATE TABLE personal (
    id_trabajador INT AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(50) NOT NULL UNIQUE,       -- Nombre de usuario para el login
    password_hash VARCHAR(255) NOT NULL,       -- Contraseña encriptada para validar acceso
    nombre_completo VARCHAR(100) NOT NULL,
    rol ENUM('Administrador', 'Pastelero', 'Vendedor') NOT NULL, -- Roles asignados
    activo TINYINT(1) DEFAULT 1
);

-- Tabla: Clientes (Cartera privada de clientes registrados en tienda o WhatsApp)
CREATE TABLE clientes (
    id_cliente INT AUTO_INCREMENT PRIMARY KEY,
    nombre_completo VARCHAR(100) NOT NULL,
    telefono VARCHAR(15),
    dni_ruc VARCHAR(11) UNIQUE,
    email VARCHAR(100) UNIQUE
);

-- =========================================================================
-- BLOQUE 2: CATÁLOGO DE PRODUCTOS Y SERVICIOS PRIVADOS
-- =========================================================================

-- Tabla: Servicios que ofrece la Pastelería (Eventos, Catering, Alquileres, Candy Bar)
CREATE TABLE servicios (
    id_servicio INT AUTO_INCREMENT PRIMARY KEY,
    nombre_servicio VARCHAR(100) NOT NULL,     -- Ejemplo: "Catering de Bocaditos", "Mesa de Dulces Temática"
    descripcion TEXT,
    precio_base DECIMAL(10, 2) NOT NULL,       -- Costo inicial del servicio
    estado_disponible TINYINT(1) DEFAULT 1     -- 1 = Activo, 0 = No disponible momentáneamente
);

-- =========================================================================
-- BLOQUE 3: CONTROL DE VENTAS Y PRODUCCIÓN TRAS BAMBALINAS
-- =========================================================================

-- Tabla: Pedidos Normales (Ventas rápidas de vitrina o stock listo: tortas enteras, porciones, etc.)
CREATE TABLE pedidos (
    id_pedido INT AUTO_INCREMENT PRIMARY KEY,
    id_cliente INT,
    fecha_venta TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    metodo_pago ENUM('Efectivo', 'Tarjeta', 'Yape/Plin') NOT NULL,
    total_pagar DECIMAL(10, 2) NOT NULL,
    id_vendedor INT,                           -- Quién del personal procesó la venta
    FOREIGN KEY (id_cliente) REFERENCES clientes(id_cliente) ON DELETE SET NULL,
    FOREIGN KEY (id_vendedor) REFERENCES personal(id_trabajador) ON DELETE SET NULL
);

-- Tabla: Pedidos Separados y Personalizados (Tortas bajo diseño con reserva previa y adelanto)
CREATE TABLE pedidos_personalizados (
    id_personalizado INT AUTO_INCREMENT PRIMARY KEY,
    id_cliente INT,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_entrega DATETIME NOT NULL,           -- Cuándo se debe recoger o enviar el pastel
    keke_sabor VARCHAR(50) NOT NULL,           -- Keke de chocolate, vainilla, zanahoria, etc.
    relleno_sabor VARCHAR(50) NOT NULL,        -- Fudge, manjarblanco, crema de fresas, etc.
    porciones INT NOT NULL,                    -- Tamaño: 15, 20, 35 porciones, etc.
    detalles_diseño TEXT,                      -- Ejemplo: "Temática de Dragon Ball, poner nombre Robertito"
    
    -- Control económico de la separación:
    monto_total DECIMAL(10, 2) NOT NULL,       -- Costo total acordado de la torta de diseño
    monto_adelanto DECIMAL(10, 2) NOT NULL,    -- Lo que el cliente pagó para separar (Mínimo 50%)
    monto_saldo DECIMAL(10, 2) GENERATED ALWAYS AS (monto_total - monto_adelanto) STORED, -- Se calcula solo
    
    estado_pago ENUM('Separado (Adelanto)', 'Cancelado (Total)') DEFAULT 'Separado (Adelanto)',
    estado_produccion ENUM('Pendiente', 'En Horno', 'Decorando', 'Listo para Entrega', 'Entregado') DEFAULT 'Pendiente',
    id_pastelero_asignado INT,                 -- Qué pastelero se encargará de este diseño
    
    FOREIGN KEY (id_cliente) REFERENCES clientes(id_cliente) ON DELETE SET NULL,
    FOREIGN KEY (id_pastelero_asignado) REFERENCES personal(id_trabajador) ON DELETE SET NULL
);
-- 1. Crear y usar la Base de Datos
CREATE DATABASE IF NOT EXISTS pasteleria_sistema_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE pasteleria_sistema_db;

-- =========================================================================
-- BLOQUE 1: SEGURIDAD, LOGIN Y PERSONAL
-- =========================================================================

-- Tabla: Personal de la empresa (Aquí se maneja el LOGIN y los ROLES)
CREATE TABLE personal (
    id_trabajador INT AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(50) NOT NULL UNIQUE,       -- Nombre de usuario para el login
    password_hash VARCHAR(255) NOT NULL,       -- Contraseña encriptada para validar acceso
    nombre_completo VARCHAR(100) NOT NULL,
    rol ENUM('Administrador', 'Pastelero', 'Vendedor') NOT NULL, -- Roles asignados
    activo TINYINT(1) DEFAULT 1
);

-- Tabla: Clientes (Cartera privada de clientes registrados en tienda o WhatsApp)
CREATE TABLE clientes (
    id_cliente INT AUTO_INCREMENT PRIMARY KEY,
    nombre_completo VARCHAR(100) NOT NULL,
    telefono VARCHAR(15),
    dni_ruc VARCHAR(11) UNIQUE,
    email VARCHAR(100) UNIQUE
);

-- =========================================================================
-- BLOQUE 2: CATÁLOGO DE PRODUCTOS Y SERVICIOS PRIVADOS
-- =========================================================================

-- Tabla: Servicios que ofrece la Pastelería (Eventos, Catering, Alquileres, Candy Bar)
CREATE TABLE servicios (
    id_servicio INT AUTO_INCREMENT PRIMARY KEY,
    nombre_servicio VARCHAR(100) NOT NULL,     -- Ejemplo: "Catering de Bocaditos", "Mesa de Dulces Temática"
    descripcion TEXT,
    precio_base DECIMAL(10, 2) NOT NULL,       -- Costo inicial del servicio
    estado_disponible TINYINT(1) DEFAULT 1     -- 1 = Activo, 0 = No disponible momentáneamente
);

-- =========================================================================
-- BLOQUE 3: CONTROL DE VENTAS Y PRODUCCIÓN TRAS BAMBALINAS
-- =========================================================================

-- Tabla: Pedidos Normales (Ventas rápidas de vitrina o stock listo: tortas enteras, porciones, etc.)
CREATE TABLE pedidos (
    id_pedido INT AUTO_INCREMENT PRIMARY KEY,
    id_cliente INT,
    fecha_venta TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    metodo_pago ENUM('Efectivo', 'Tarjeta', 'Yape/Plin') NOT NULL,
    total_pagar DECIMAL(10, 2) NOT NULL,
    id_vendedor INT,                           -- Quién del personal procesó la venta
    FOREIGN KEY (id_cliente) REFERENCES clientes(id_cliente) ON DELETE SET NULL,
    FOREIGN KEY (id_vendedor) REFERENCES personal(id_trabajador) ON DELETE SET NULL
);

-- Tabla: Pedidos Separados y Personalizados (Tortas bajo diseño con reserva previa y adelanto)
CREATE TABLE pedidos_personalizados (
    id_personalizado INT AUTO_INCREMENT PRIMARY KEY,
    id_cliente INT,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_entrega DATETIME NOT NULL,           -- Cuándo se debe recoger o enviar el pastel
    keke_sabor VARCHAR(50) NOT NULL,           -- Keke de chocolate, vainilla, zanahoria, etc.
    relleno_sabor VARCHAR(50) NOT NULL,        -- Fudge, manjarblanco, crema de fresas, etc.
    porciones INT NOT NULL,                    -- Tamaño: 15, 20, 35 porciones, etc.
    detalles_diseño TEXT,                      -- Ejemplo: "Temática de Dragon Ball, poner nombre Robertito"
    
    -- Control económico de la separación:
    monto_total DECIMAL(10, 2) NOT NULL,       -- Costo total acordado de la torta de diseño
    monto_adelanto DECIMAL(10, 2) NOT NULL,    -- Lo que el cliente pagó para separar (Mínimo 50%)
    monto_saldo DECIMAL(10, 2) GENERATED ALWAYS AS (monto_total - monto_adelanto) STORED, -- Se calcula solo
    
    estado_pago ENUM('Separado (Adelanto)', 'Cancelado (Total)') DEFAULT 'Separado (Adelanto)',
    estado_produccion ENUM('Pendiente', 'En Horno', 'Decorando', 'Listo para Entrega', 'Entregado') DEFAULT 'Pendiente',
    id_pastelero_asignado INT,                 -- Qué pastelero se encargará de este diseño
    
    FOREIGN KEY (id_cliente) REFERENCES clientes(id_cliente) ON DELETE SET NULL,
    FOREIGN KEY (id_pastelero_asignado) REFERENCES personal(id_trabajador) ON DELETE SET NULL
);
-- 1. Crear y usar la Base de Datos
CREATE DATABASE IF NOT EXISTS pasteleria_sistema_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE pasteleria_sistema_db;

-- =========================================================================
-- BLOQUE 1: SEGURIDAD, LOGIN Y PERSONAL
-- =========================================================================

-- Tabla: Personal de la empresa (Aquí se maneja el LOGIN y los ROLES)
CREATE TABLE personal (
    id_trabajador INT AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(50) NOT NULL UNIQUE,       -- Nombre de usuario para el login
    password_hash VARCHAR(255) NOT NULL,       -- Contraseña encriptada para validar acceso
    nombre_completo VARCHAR(100) NOT NULL,
    rol ENUM('Administrador', 'Pastelero', 'Vendedor') NOT NULL, -- Roles asignados
    activo TINYINT(1) DEFAULT 1
);

-- Tabla: Clientes (Cartera privada de clientes registrados en tienda o WhatsApp)
CREATE TABLE clientes (
    id_cliente INT AUTO_INCREMENT PRIMARY KEY,
    nombre_completo VARCHAR(100) NOT NULL,
    telefono VARCHAR(15),
    dni_ruc VARCHAR(11) UNIQUE,
    email VARCHAR(100) UNIQUE
);

-- =========================================================================
-- BLOQUE 2: CATÁLOGO DE PRODUCTOS Y SERVICIOS PRIVADOS
-- =========================================================================

-- Tabla: Servicios que ofrece la Pastelería (Eventos, Catering, Alquileres, Candy Bar)
CREATE TABLE servicios (
    id_servicio INT AUTO_INCREMENT PRIMARY KEY,
    nombre_servicio VARCHAR(100) NOT NULL,     -- Ejemplo: "Catering de Bocaditos", "Mesa de Dulces Temática"
    descripcion TEXT,
    precio_base DECIMAL(10, 2) NOT NULL,       -- Costo inicial del servicio
    estado_disponible TINYINT(1) DEFAULT 1     -- 1 = Activo, 0 = No disponible momentáneamente
);

-- =========================================================================
-- BLOQUE 3: CONTROL DE VENTAS Y PRODUCCIÓN TRAS BAMBALINAS
-- =========================================================================

-- Tabla: Pedidos Normales (Ventas rápidas de vitrina o stock listo: tortas enteras, porciones, etc.)
CREATE TABLE pedidos (
    id_pedido INT AUTO_INCREMENT PRIMARY KEY,
    id_cliente INT,
    fecha_venta TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    metodo_pago ENUM('Efectivo', 'Tarjeta', 'Yape/Plin') NOT NULL,
    total_pagar DECIMAL(10, 2) NOT NULL,
    id_vendedor INT,                           -- Quién del personal procesó la venta
    FOREIGN KEY (id_cliente) REFERENCES clientes(id_cliente) ON DELETE SET NULL,
    FOREIGN KEY (id_vendedor) REFERENCES personal(id_trabajador) ON DELETE SET NULL
);

-- Tabla: Pedidos Separados y Personalizados (Tortas bajo diseño con reserva previa y adelanto)
CREATE TABLE pedidos_personalizados (
    id_personalizado INT AUTO_INCREMENT PRIMARY KEY,
    id_cliente INT,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_entrega DATETIME NOT NULL,           -- Cuándo se debe recoger o enviar el pastel
    keke_sabor VARCHAR(50) NOT NULL,           -- Keke de chocolate, vainilla, zanahoria, etc.
    relleno_sabor VARCHAR(50) NOT NULL,        -- Fudge, manjarblanco, crema de fresas, etc.
    porciones INT NOT NULL,                    -- Tamaño: 15, 20, 35 porciones, etc.
    detalles_diseño TEXT,                      -- Ejemplo: "Temática de Dragon Ball, poner nombre Robertito"
    
    -- Control económico de la separación:
    monto_total DECIMAL(10, 2) NOT NULL,       -- Costo total acordado de la torta de diseño
    monto_adelanto DECIMAL(10, 2) NOT NULL,    -- Lo que el cliente pagó para separar (Mínimo 50%)
    monto_saldo DECIMAL(10, 2) GENERATED ALWAYS AS (monto_total - monto_adelanto) STORED, -- Se calcula solo
    
    estado_pago ENUM('Separado (Adelanto)', 'Cancelado (Total)') DEFAULT 'Separado (Adelanto)',
    estado_produccion ENUM('Pendiente', 'En Horno', 'Decorando', 'Listo para Entrega', 'Entregado') DEFAULT 'Pendiente',
    id_pastelero_asignado INT,                 -- Qué pastelero se encargará de este diseño
    
    FOREIGN KEY (id_cliente) REFERENCES clientes(id_cliente) ON DELETE SET NULL,
    FOREIGN KEY (id_pastelero_asignado) REFERENCES personal(id_trabajador) ON DELETE SET NULL
);

INSERT INTO personal (usuario, password_hash, nombre_completo, rol) VALUES
('augusto_admin', 'admin789', 'Augusto (Dueño/Admin)', 'Administrador'),
('maria_chef', 'cake2026', 'María Solís (Pastelera)', 'Pastelero'),
('carlos_caja', 'cajero456', 'Carlos Pérez (Cajero)', 'Vendedor');

