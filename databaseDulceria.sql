CREATE DATABASE dona_solina ;
USE dona_solina;

-- Tabla independiente para el Login
CREATE TABLE usuario (
    id_usuario INT PRIMARY KEY AUTO_INCREMENT,
    nombre_usuario VARCHAR(50),
    correo VARCHAR(100),
    contrasena VARCHAR(255),
    rol VARCHAR(20)
);

CREATE TABLE cliente (
    id_cliente INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(50),
    apellido VARCHAR(50),
    dni CHAR(8),
    telefono VARCHAR(15),
    direccion VARCHAR(100),
    correo VARCHAR(100)
);

CREATE TABLE producto_regional (
    id_producto INT PRIMARY KEY AUTO_INCREMENT,
    nombre_producto VARCHAR(100),
    categoria VARCHAR(50),
    region_origen VARCHAR(50),
    precio DECIMAL(10,2),
    stock INT,
    descripcion TEXT
);

CREATE TABLE pedido (
    id_pedido INT PRIMARY KEY AUTO_INCREMENT,
    id_cliente INT,
    id_producto INT,
    fecha_pedido DATE,
    cantidad INT,
    total DECIMAL(10,2),
    estado_pedido VARCHAR(20),
    FOREIGN KEY (id_cliente) REFERENCES cliente(id_cliente),
    FOREIGN KEY (id_producto) REFERENCES producto_regional(id_producto)
);

insert into usuario(nombre_usuario, correo, contrasena, rol)
values ('Dulceria', 'augustoipushima06@gmail.com', '1234', 'Encargada');
select * from usuario;
