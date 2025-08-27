-- Base de datos para Sistema de Control de Gastos
-- MySQL 5.7

DROP DATABASE IF EXISTS control_gastos;
CREATE DATABASE control_gastos CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE control_gastos;

-- Tabla de usuarios
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    rol ENUM('admin', 'usuario') DEFAULT 'usuario',
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabla de categorías
CREATE TABLE categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    color VARCHAR(7) DEFAULT '#007bff', -- Color hexadecimal
    icono VARCHAR(50) DEFAULT 'fas fa-tag', -- Clase de icono Font Awesome
    categoria_padre_id INT NULL, -- Para subcategorías
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (categoria_padre_id) REFERENCES categorias(id) ON DELETE SET NULL
);

-- Tabla de métodos de pago
CREATE TABLE metodos_pago (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    descripcion TEXT,
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de gastos
CREATE TABLE gastos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    categoria_id INT NOT NULL,
    metodo_pago_id INT NOT NULL,
    monto DECIMAL(10,2) NOT NULL,
    descripcion TEXT,
    fecha_gasto DATE NOT NULL,
    comprobante VARCHAR(255) NULL, -- Ruta del archivo adjunto
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE RESTRICT,
    FOREIGN KEY (metodo_pago_id) REFERENCES metodos_pago(id) ON DELETE RESTRICT
);

-- Tabla de presupuestos
CREATE TABLE presupuestos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    categoria_id INT NOT NULL,
    monto_limite DECIMAL(10,2) NOT NULL,
    periodo ENUM('mensual', 'anual') DEFAULT 'mensual',
    mes INT NULL, -- 1-12 para presupuesto mensual
    año INT NOT NULL,
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE CASCADE
);

-- Tabla de alertas de presupuesto
CREATE TABLE alertas_presupuesto (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    presupuesto_id INT NOT NULL,
    porcentaje_alerta INT DEFAULT 80, -- Porcentaje del presupuesto para activar alerta
    mensaje TEXT,
    fecha_alerta TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    leida BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (presupuesto_id) REFERENCES presupuestos(id) ON DELETE CASCADE
);

-- Índices para optimización
CREATE INDEX idx_gastos_usuario_fecha ON gastos(usuario_id, fecha_gasto);
CREATE INDEX idx_gastos_categoria ON gastos(categoria_id);
CREATE INDEX idx_gastos_fecha ON gastos(fecha_gasto);
CREATE INDEX idx_presupuestos_usuario_periodo ON presupuestos(usuario_id, año, mes);

-- Datos de ejemplo

-- Usuario administrador por defecto
INSERT INTO usuarios (nombre, email, password, rol) VALUES 
('Administrador', 'admin@gastos.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');
-- Contraseña: password

-- Usuarios de ejemplo
INSERT INTO usuarios (nombre, email, password, rol) VALUES 
('Juan Pérez', 'juan@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'usuario'),
('María García', 'maria@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'usuario');
-- Contraseña: password

-- Métodos de pago
INSERT INTO metodos_pago (nombre, descripcion) VALUES 
('Efectivo', 'Pago en efectivo'),
('Tarjeta de Débito', 'Pago con tarjeta de débito'),
('Tarjeta de Crédito', 'Pago con tarjeta de crédito'),
('Transferencia', 'Transferencia bancaria'),
('Cheque', 'Pago con cheque');

-- Categorías principales
INSERT INTO categorias (nombre, descripcion, color, icono) VALUES 
('Alimentación', 'Gastos relacionados con comida y bebidas', '#28a745', 'fas fa-utensils'),
('Transporte', 'Gastos de movilidad y combustible', '#007bff', 'fas fa-car'),
('Servicios', 'Servicios básicos como luz, agua, internet', '#ffc107', 'fas fa-bolt'),
('Ocio', 'Entretenimiento y actividades recreativas', '#e83e8c', 'fas fa-gamepad'),
('Salud', 'Gastos médicos y farmacéuticos', '#dc3545', 'fas fa-heart'),
('Educación', 'Gastos educativos y capacitación', '#6f42c1', 'fas fa-graduation-cap'),
('Hogar', 'Gastos del hogar y mantenimiento', '#fd7e14', 'fas fa-home'),
('Ropa', 'Vestimenta y accesorios', '#20c997', 'fas fa-tshirt');

-- Subcategorías de Alimentación
INSERT INTO categorias (nombre, descripcion, color, icono, categoria_padre_id) VALUES 
('Supermercado', 'Compras en supermercado', '#28a745', 'fas fa-shopping-cart', 1),
('Restaurantes', 'Comidas en restaurantes', '#28a745', 'fas fa-pizza-slice', 1),
('Comida Rápida', 'Fast food y delivery', '#28a745', 'fas fa-hamburger', 1);

-- Subcategorías de Transporte
INSERT INTO categorias (nombre, descripcion, color, icono, categoria_padre_id) VALUES 
('Combustible', 'Gasolina y diesel', '#007bff', 'fas fa-gas-pump', 2),
('Transporte Público', 'Autobús, metro, taxi', '#007bff', 'fas fa-bus', 2),
('Mantenimiento Vehículo', 'Reparaciones y servicios', '#007bff', 'fas fa-wrench', 2);

-- Gastos de ejemplo para el mes actual
INSERT INTO gastos (usuario_id, categoria_id, metodo_pago_id, monto, descripcion, fecha_gasto) VALUES 
(2, 9, 1, 850.00, 'Compra semanal en supermercado', CURDATE() - INTERVAL 2 DAY),
(2, 12, 2, 35.00, 'Gasolina', CURDATE() - INTERVAL 1 DAY),
(2, 10, 3, 280.00, 'Cena en restaurante', CURDATE() - INTERVAL 3 DAY),
(2, 5, 1, 450.00, 'Consulta médica', CURDATE() - INTERVAL 5 DAY),
(2, 3, 4, 1200.00, 'Pago de luz', CURDATE() - INTERVAL 7 DAY),
(3, 9, 1, 720.00, 'Despensa mensual', CURDATE() - INTERVAL 1 DAY),
(3, 13, 2, 45.00, 'Transporte público', CURDATE() - INTERVAL 2 DAY),
(3, 4, 3, 180.00, 'Cine', CURDATE() - INTERVAL 4 DAY);

-- Presupuestos de ejemplo
INSERT INTO presupuestos (usuario_id, categoria_id, monto_limite, periodo, mes, año) VALUES 
(2, 1, 3000.00, 'mensual', MONTH(CURDATE()), YEAR(CURDATE())),
(2, 2, 1500.00, 'mensual', MONTH(CURDATE()), YEAR(CURDATE())),
(2, 3, 2000.00, 'mensual', MONTH(CURDATE()), YEAR(CURDATE())),
(3, 1, 2500.00, 'mensual', MONTH(CURDATE()), YEAR(CURDATE())),
(3, 2, 1000.00, 'mensual', MONTH(CURDATE()), YEAR(CURDATE()));