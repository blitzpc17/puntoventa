show databases;



create database db_puntoventa

use db_puntoventa

-- Tabla de clientes
CREATE TABLE clientes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100),
    telefono VARCHAR(15),
    email VARCHAR(100),
    direccion TEXT,
    rfc VARCHAR(13) null,
    limite_fiado DECIMAL(10,2),
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    created_at TIMESTAMP not NULL,    
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
     deleted_at TIMESTAMP NULL,   
    UNIQUE KEY clientes_email_unique (email),
    UNIQUE KEY clientes_rfc_unique (rfc)
);

-- Tabla de proveedores
CREATE TABLE proveedores (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100),
    rfc VARCHAR(13) null,
    contacto VARCHAR(100),
    telefono VARCHAR(15),
    email VARCHAR(100),
    direccion TEXT,
    notas text null,
    created_at TIMESTAMP not null,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL DEFAULT NULL
);



-- Tabla de compras
CREATE TABLE compras (
    id INT PRIMARY KEY AUTO_INCREMENT,
    folio VARCHAR(20) UNIQUE,
    proveedor_id INT NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    fecha TIMESTAMP NOT NULL,
    estado ENUM('pendiente', 'completada', 'cancelada') DEFAULT 'completada',
    notas TEXT NULL,    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,   
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (proveedor_id) REFERENCES proveedores(id)
);

create table categorias_producto(
	  id INT PRIMARY KEY AUTO_INCREMENT,
	  nombre VARCHAR(100)
);

-- Tabla de productos
CREATE TABLE productos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    codigo varchar(50) null,
    proveedor_Id int not null,
    categoriaId int  not null,
    nombre VARCHAR(100),    
    descripcion TEXT,
    precio_compra DECIMAL(10,2),
    precio_venta DECIMAL(10,2),
    precio_mayoreo DECIMAL(10,2),
    existencia INT,
    min_existencia INT,
    created_at TIMESTAMP not null,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,  
    foreign key (categoriaId) references categorias_producto(id),
    foreign key (proveedor_Id) references proveedores(id)
 
);


-- Tabla de detalle_compras
CREATE TABLE detalle_compras (
    id INT PRIMARY KEY AUTO_INCREMENT,
    compra_id INT,
    producto_id INT,
    cantidad INT,
    precio DECIMAL(10,2),
    subtotal DECIMAL(10,2),
    created_at TIMESTAMP not null,
    updated_at TIMESTAMP null,
    FOREIGN KEY (compra_id) REFERENCES compras(id),
    FOREIGN KEY (producto_id) REFERENCES productos(id)
);








CREATE TABLE ventas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    cliente_id INT NULL,
  --  usuario_id INT NOT NULL,
    folio VARCHAR(20) NOT NULL UNIQUE,
    total DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    efectivo DECIMAL(12,2) NULL,
    cambio DECIMAL(12,2) NULL,
    tipo_pago ENUM('efectivo', 'tarjeta', 'fiado') NOT NULL DEFAULT 'efectivo',
    estado ENUM('pendiente', 'completada', 'cancelada') NOT NULL DEFAULT 'completada',
    fecha TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP not null DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,   
    FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE SET NULL
   -- FOREIGN KEY (usuario_id) REFERENCES users(id) ON DELETE RESTRICT
); 


-- Tabla de detalle_ventas
CREATE TABLE detalle_ventas (
    id INT PRIMARY KEY auto_increment,
    venta_id INT,
    producto_id INT,
    cantidad INT,
    precio DECIMAL(10,2),
    subtotal DECIMAL(10,2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, 
    FOREIGN KEY (venta_id) REFERENCES ventas(id),
    FOREIGN KEY (producto_id) REFERENCES productos(id)
);

-- Tabla de fiados
CREATE TABLE fiados (
    id INT PRIMARY KEY AUTO_INCREMENT,
    cliente_id INT,
    venta_id INT,
    monto DECIMAL(10,2),
    saldo_pendiente DECIMAL(10,2),
    estado ENUM('pendiente', 'pagado'),
    fecha_limite DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, 
    deleted_at TIMESTAMP null,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id),
    FOREIGN KEY (venta_id) REFERENCES ventas(id)
);


-- abonos fiados
CREATE TABLE abonos_fiado (
    id INT PRIMARY KEY AUTO_INCREMENT,
    fiado_id INT NOT NULL,
    monto DECIMAL(10,2) NOT NULL,
    notas TEXT NULL,
    created_at TIMESTAMP not NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (fiado_id) REFERENCES fiados(id) ON DELETE CASCADE
);




-- Tabla de cortes_caja
CREATE TABLE cortes_caja (
    id INT PRIMARY KEY AUTO_INCREMENT,
    -- usuario_id INT NOT NULL,
    fecha DATE NOT NULL,
    monto_inicial DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    monto_final DECIMAL(12,2) NULL,
    ventas_efectivo DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    ventas_tarjeta DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    ventas_fiado DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    total_ventas DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    diferencia DECIMAL(12,2) NULL,
    estado ENUM('abierto', 'cerrado') DEFAULT 'abierto',
    notas TEXT NULL,
    created_at TIMESTAMP not NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL
    -- FOREIGN KEY (usuario_id) REFERENCES users(id) ON DELETE RESTRICT -- no hay tabla usuarios
);






select *from categorias_producto


