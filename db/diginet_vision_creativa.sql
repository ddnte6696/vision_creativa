-- phpMyAdmin SQL Dump - VISION CREATIVA SEMI-SANDBOX
-- Base de datos: diginet_vision_creativa
-- Servidor: omnibus-guadalajara.com (PRODUCCIÓN usado como SEMI-SANDBOX)
-- Mercado Pago: Credenciales TEST con HTTPS real
-- Fecha: 30-09-2025

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- ============================================================================
-- CONFIGURACIÓN SEMI-SANDBOX (TEST EN SERVIDOR HTTPS)
-- ============================================================================

-- Crear base de datos si no existe
CREATE DATABASE IF NOT EXISTS `diginet_vision_creativa` 
DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

USE `diginet_vision_creativa`;

-- ============================================================================
-- ESTRUCTURA DE TABLAS
-- ============================================================================

-- Tabla: atributos_color
CREATE TABLE IF NOT EXISTS `atributos_color` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `codigo_hex` varchar(7) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_nombre_color` (`nombre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tabla: atributos_spec
CREATE TABLE IF NOT EXISTS `atributos_spec` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_atributo` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_nombre_atributo` (`nombre_atributo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tabla: atributos_tela
CREATE TABLE IF NOT EXISTS `atributos_tela` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `url_textura` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tabla: disenos_base
CREATE TABLE IF NOT EXISTS `disenos_base` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `url_imagen_plantilla` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tabla: usuarios
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `rol` enum('admin','b2b_client','b2c_client') NOT NULL,
  `datos_empresa` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`datos_empresa`)),
  `datos_envio` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`datos_envio`)),
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tabla: direcciones
CREATE TABLE IF NOT EXISTS `direcciones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) NOT NULL,
  `calle` varchar(255) NOT NULL,
  `colonia` varchar(100) NOT NULL,
  `ciudad` varchar(100) NOT NULL,
  `estado` varchar(50) NOT NULL,
  `cp` varchar(10) NOT NULL,
  `referencias` text DEFAULT NULL,
  `is_default` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_usuario_id` (`usuario_id`),
  CONSTRAINT `direcciones_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tabla: productos
CREATE TABLE IF NOT EXISTS `productos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sku` varchar(100) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `precio_base` decimal(10,2) NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `origen` enum('interno','b2b_custom') NOT NULL DEFAULT 'interno',
  `id_proyecto_b2b_origen` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_sku` (`sku`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tabla: variaciones_producto
CREATE TABLE IF NOT EXISTS `variaciones_producto` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_producto` int(11) NOT NULL,
  `id_color` int(11) NOT NULL,
  `url_imagen` varchar(255) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `precio_adicional` decimal(10,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_producto_color` (`id_producto`,`id_color`),
  KEY `fk_variacion_color` (`id_color`),
  CONSTRAINT `variaciones_producto_ibfk_1` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_variacion_color` FOREIGN KEY (`id_color`) REFERENCES `atributos_color` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tabla: producto_specs
CREATE TABLE IF NOT EXISTS `producto_specs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_producto` int(11) NOT NULL,
  `id_spec` int(11) NOT NULL,
  `valor` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_producto_spec` (`id_producto`,`id_spec`),
  KEY `fk_spec_atributo` (`id_spec`),
  CONSTRAINT `producto_specs_ibfk_1` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_spec_atributo` FOREIGN KEY (`id_spec`) REFERENCES `atributos_spec` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================================
-- TABLAS SISTEMA B2B
-- ============================================================================

-- Tabla: proyectos_b2b
CREATE TABLE IF NOT EXISTS `proyectos_b2b` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_cliente_b2b` int(11) NOT NULL,
  `nombre_proyecto` varchar(255) NOT NULL,
  `requerimientos` text NOT NULL,
  `presupuesto` decimal(12,2) DEFAULT NULL,
  `cantidad_piezas` int(11) NOT NULL,
  `estado` enum('propuesta','cotizacion','contrato','pago_anticipo','produccion','envio','completado','cancelado') NOT NULL DEFAULT 'propuesta',
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_id_cliente_b2b` (`id_cliente_b2b`),
  CONSTRAINT `fk_proyecto_cliente` FOREIGN KEY (`id_cliente_b2b`) REFERENCES `usuarios` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tabla: configuracion_proyecto_b2b
CREATE TABLE IF NOT EXISTS `configuracion_proyecto_b2b` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_proyecto_b2b` int(11) NOT NULL,
  `id_color` int(11) DEFAULT NULL,
  `id_tela` int(11) DEFAULT NULL,
  `id_diseno_base` int(11) DEFAULT NULL,
  `personalizaciones` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`personalizaciones`)),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_id_proyecto_b2b` (`id_proyecto_b2b`),
  KEY `fk_config_color` (`id_color`),
  KEY `fk_config_tela` (`id_tela`),
  KEY `fk_config_diseno` (`id_diseno_base`),
  CONSTRAINT `fk_config_proyecto` FOREIGN KEY (`id_proyecto_b2b`) REFERENCES `proyectos_b2b` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_config_color` FOREIGN KEY (`id_color`) REFERENCES `atributos_color` (`id`),
  CONSTRAINT `fk_config_tela` FOREIGN KEY (`id_tela`) REFERENCES `atributos_tela` (`id`),
  CONSTRAINT `fk_config_diseno` FOREIGN KEY (`id_diseno_base`) REFERENCES `disenos_base` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tabla: cotizaciones_b2b
CREATE TABLE IF NOT EXISTS `cotizaciones_b2b` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_proyecto_b2b` int(11) NOT NULL,
  `rangos_precio` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`rangos_precio`)),
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_id_proyecto_b2b_cot` (`id_proyecto_b2b`),
  CONSTRAINT `cotizaciones_b2b_ibfk_1` FOREIGN KEY (`id_proyecto_b2b`) REFERENCES `proyectos_b2b` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tabla: contratos_b2b
CREATE TABLE IF NOT EXISTS `contratos_b2b` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_proyecto_b2b` int(11) NOT NULL,
  `contenido_contrato` longtext NOT NULL,
  `firma_digital_cliente` text DEFAULT NULL,
  `fecha_firma` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_id_proyecto_b2b_contrato` (`id_proyecto_b2b`),
  CONSTRAINT `contratos_b2b_ibfk_1` FOREIGN KEY (`id_proyecto_b2b`) REFERENCES `proyectos_b2b` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tabla: chat_mensajes
CREATE TABLE IF NOT EXISTS `chat_mensajes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_proyecto_b2b` int(11) NOT NULL,
  `id_remitente` int(11) NOT NULL,
  `mensaje` text NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_chat_proyecto` (`id_proyecto_b2b`),
  CONSTRAINT `chat_mensajes_ibfk_1` FOREIGN KEY (`id_proyecto_b2b`) REFERENCES `proyectos_b2b` (`id`) ON DELETE CASCADE,
  CONSTRAINT `chat_mensajes_ibfk_2` FOREIGN KEY (`id_remitente`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================================
-- TABLAS SISTEMA DE PEDIDOS Y PAGOS
-- ============================================================================

-- Tabla: pedidos
CREATE TABLE IF NOT EXISTS `pedidos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) NOT NULL,
  `tipo_pedido` enum('b2b','b2c') NOT NULL,
  `id_referencia` int(11) DEFAULT NULL,
  `external_reference` varchar(100) DEFAULT NULL,
  `preference_id` varchar(100) DEFAULT NULL,
  `payment_id` varchar(100) DEFAULT NULL,
  `monto_total` decimal(12,2) NOT NULL,
  `shipping_cost` decimal(10,2) DEFAULT 0.00,
  `tax_amount` decimal(10,2) DEFAULT 0.00,
  `shipping_address_id` int(11) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `estado_pago` enum('pendiente','anticipo_pagado','pagado_completo','reembolsado','paid','failed','pending') NOT NULL DEFAULT 'pendiente',
  `payment_method` varchar(50) DEFAULT NULL,
  `estado_envio` enum('preparando','enviado','entregado','cancelado') NOT NULL DEFAULT 'preparando',
  `fecha_pedido` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `external_reference` (`external_reference`),
  KEY `idx_id_usuario_pedido` (`id_usuario`),
  KEY `idx_external_reference` (`external_reference`),
  KEY `idx_payment_id` (`payment_id`),
  KEY `idx_preference_id` (`preference_id`),
  KEY `shipping_address_id` (`shipping_address_id`),
  CONSTRAINT `pedidos_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  CONSTRAINT `pedidos_ibfk_2` FOREIGN KEY (`shipping_address_id`) REFERENCES `direcciones` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tabla: pedido_items
CREATE TABLE IF NOT EXISTS `pedido_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pedido_id` int(11) NOT NULL,
  `variacion_id` int(11) DEFAULT NULL,
  `producto_nombre` varchar(255) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `opciones_personalizacion` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`opciones_personalizacion`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_pedido_id` (`pedido_id`),
  KEY `idx_variacion_id` (`variacion_id`),
  CONSTRAINT `pedido_items_ibfk_1` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `pedido_items_ibfk_2` FOREIGN KEY (`variacion_id`) REFERENCES `variaciones_producto` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================================
-- TABLAS MERCADO PAGO - CONFIGURACIÓN SEMI-SANDBOX
-- ============================================================================

-- Tabla: mp_config
CREATE TABLE IF NOT EXISTS `mp_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `environment` enum('sandbox','production') DEFAULT 'sandbox',
  `access_token` text NOT NULL,
  `public_key` text NOT NULL,
  `client_id` varchar(100) DEFAULT NULL,
  `client_secret` text DEFAULT NULL,
  `webhook_url` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tabla: mp_notifications
CREATE TABLE IF NOT EXISTS `mp_notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `payment_id` varchar(100) NOT NULL,
  `topic` varchar(50) NOT NULL,
  `status` varchar(50) NOT NULL,
  `external_reference` varchar(100) DEFAULT NULL,
  `merchant_order_id` varchar(100) DEFAULT NULL,
  `preference_id` varchar(100) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `currency` varchar(3) DEFAULT 'MXN',
  `date_created` datetime DEFAULT NULL,
  `date_approved` datetime DEFAULT NULL,
  `raw_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`raw_data`)),
  `processed` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_payment_id` (`payment_id`),
  KEY `idx_external_reference` (`external_reference`),
  KEY `idx_status` (`status`),
  KEY `idx_processed` (`processed`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tabla: carritos_abandonados
CREATE TABLE IF NOT EXISTS `carritos_abandonados` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) DEFAULT NULL,
  `preference_id` varchar(100) DEFAULT NULL,
  `external_reference` varchar(100) DEFAULT NULL,
  `cart_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`cart_data`)),
  `shipping_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`shipping_data`)),
  `total_amount` decimal(10,2) DEFAULT NULL,
  `email_sent` tinyint(1) DEFAULT 0,
  `recovered` tinyint(1) DEFAULT 0,
  `expires_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_usuario_id` (`usuario_id`),
  KEY `idx_preference_id` (`preference_id`),
  KEY `idx_external_reference` (`external_reference`),
  KEY `idx_recovered` (`recovered`),
  KEY `idx_expires_at` (`expires_at`),
  CONSTRAINT `carritos_abandonados_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tabla: transaction_log
CREATE TABLE IF NOT EXISTS `transaction_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `external_reference` varchar(100) DEFAULT NULL,
  `action` varchar(50) NOT NULL,
  `details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`details`)),
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_external_reference` (`external_reference`),
  KEY `idx_action` (`action`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================================
-- TABLA CRM
-- ============================================================================

-- Tabla: crm_interacciones
CREATE TABLE IF NOT EXISTS `crm_interacciones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) NOT NULL,
  `tipo_interaccion` varchar(100) NOT NULL,
  `detalle` text DEFAULT NULL,
  `id_referencia_asociada` int(11) DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_crm_usuario` (`id_usuario`),
  CONSTRAINT `crm_interacciones_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================================
-- INSERCIÓN DE DATOS BASE
-- ============================================================================

-- Colores base
INSERT IGNORE INTO `atributos_color` (`id`, `nombre`, `codigo_hex`) VALUES
(10, 'Verde Esmeralda', '#2ECC71'),
(11, 'Rojo Intenso', '#E74C3C'),
(12, 'Azul Zafiro', '#3498DB'),
(13, 'Vino Borgoña', '#943126'),
(14, 'Naranja Vital', '#F39C12'),
(15, 'Negro Clásico', '#1C1C1C'),
(16, 'Gris Grafito', '#5D6D7E'),
(17, 'Blanco Nieve', '#FDFEFE');

-- Especificaciones de productos
INSERT IGNORE INTO `atributos_spec` (`id`, `nombre_atributo`) VALUES
(1, 'Material'),
(2, 'Dimensiones'),
(3, 'Volumen'),
(4, 'Peso'),
(5, 'Compartimentos'),
(6, 'Laptop'),
(7, 'Extras'),
(8, 'Cierres');

-- Telas disponibles
INSERT IGNORE INTO `atributos_tela` (`id`, `nombre`, `descripcion`, `url_textura`) VALUES
(1, 'Poliéster 600D', 'Un material estándar en la industria, conocido por su alta durabilidad, resistencia a la abrasión y al agua. Ideal para uso rudo y diario.', 'img/textures/polyester_600d.jpg'),
(2, 'Nylon Ripstop', 'Tejido ligero y antidesgarro que incorpora hilos de refuerzo para una mayor durabilidad. Perfecto para productos que requieren ligereza y resistencia.', 'img/textures/nylon_ripstop.jpg'),
(3, 'Lona de Algodón (Canvas)', 'Ofrece un aspecto clásico y robusto con una textura natural. Excelente para productos con un enfoque en el estilo y la estética vintage.', 'img/textures/canvas_cotton.jpg'),
(4, 'Piel Sintética (PU)', 'Una alternativa vegana al cuero que proporciona un acabado premium y elegante. Es fácil de limpiar y resistente a las manchas.', 'img/textures/pu_leather.jpg');

-- Diseños base
INSERT IGNORE INTO `disenos_base` (`id`, `nombre`, `descripcion`, `url_imagen_plantilla`) VALUES
(1, 'Mochila Urbana "Commuter"', 'Diseño moderno y funcional con compartimento acolchado para laptop, múltiples bolsillos organizadores y un perfil delgado. Ideal para la oficina y la ciudad.', 'img/designs/base_commuter.png'),
(2, 'Maletín Ejecutivo "Legacy"', 'Un diseño clásico y profesional para transportar documentos, laptop y accesorios. Estructura semi-rígida que proyecta elegancia y seriedad.', 'img/designs/base_legacy.png'),
(3, 'Bolsa de Viaje "Weekender"', 'Espaciosa y versátil, diseñada para escapadas de fin de semana. Combina un gran compartimento principal con bolsillos de fácil acceso.', 'img/designs/base_weekender.png'),
(4, 'Mochila Saco "Sprint"', 'Un diseño simple, ultraligero y práctico. Perfecto para eventos deportivos, promocionales o como una opción económica y de gran volumen.', 'img/designs/base_sprint.png');

-- Usuario de prueba con dirección
INSERT IGNORE INTO `usuarios` (`id`, `nombre`, `email`, `telefono`, `password_hash`, `rol`) VALUES
(1, 'Micaela Cueva Sanchez', 'micuevas@gmail.com', '3331234567', '$2y$10$example_hash_here', 'b2c_client'),
(2, 'Hector Garcia', 'hmge6696@gmail.com', '3337654321', '$2y$10$example_hash_here', 'b2c_client');

-- Direcciones de prueba
INSERT IGNORE INTO `direcciones` (`id`, `usuario_id`, `calle`, `colonia`, `ciudad`, `estado`, `cp`, `is_default`) VALUES
(1, 1, 'Av. Revolución 1234', 'Centro', 'Guadalajara', 'Jalisco', '44100', 1),
(2, 2, 'Calle Morelos 456', 'Americana', 'Guadalajara', 'Jalisco', '44160', 1);

-- Producto ejemplo
INSERT IGNORE INTO `productos` (`id`, `sku`, `nombre`, `descripcion`, `precio_base`, `activo`, `origen`) VALUES
(1, 'VC-SCH-001', 'Mochila Escolar "Explorer"', 'La compañera ideal para la aventura diaria del aprendizaje. Diseñada para resistir el ritmo de la vida estudiantil, la mochila "Explorer" combina durabilidad, comodidad y un estilo vibrante para que cada día sea una nueva expedición.', 599.00, 1, 'interno');

-- Especificaciones del producto
INSERT IGNORE INTO `producto_specs` (`id`, `id_producto`, `id_spec`, `valor`) VALUES
(1, 1, 1, 'Poliéster 600D de alta resistencia'),
(2, 1, 2, '45cm (alto) x 32cm (ancho) x 18cm (prof.)'),
(3, 1, 3, '25 Litros'),
(4, 1, 4, '0.6 kg'),
(5, 1, 5, '3 (Principal, frontal y para laptop)'),
(6, 1, 6, 'Acolchado, hasta 15.6 pulgadas'),
(7, 1, 7, 'Espalda ergonómica, bolsillos laterales'),
(8, 1, 8, 'Reforzados de alta durabilidad');

-- Variaciones del producto
INSERT IGNORE INTO `variaciones_producto` (`id`, `id_producto`, `id_color`, `url_imagen`, `stock`, `precio_adicional`) VALUES
(1, 1, 10, 'img/productos/mochila-escolar-verde.jpg', 50, 0.00),
(2, 1, 11, 'img/productos/mochila-escolar-rojo.jpg', 45, 0.00),
(3, 1, 12, 'img/productos/mochila-escolar-azul.jpg', 60, 0.00),
(4, 1, 13, 'img/productos/mochila-escolar-vino.jpg', 30, 0.00),
(5, 1, 14, 'img/productos/mochila-escolar-naranja.jpg', 25, 0.00),
(6, 1, 15, 'img/productos/mochila-escolar-negro.jpg', 70, 0.00);

-- ============================================================================
-- CONFIGURACIÓN MERCADO PAGO SEMI-SANDBOX (TEST EN SERVIDOR HTTPS)
-- ============================================================================

-- Configuración MP con tus credenciales TEST pero en servidor HTTPS real
INSERT IGNORE INTO `mp_config` (`id`, `environment`, `access_token`, `public_key`, `webhook_url`, `is_active`) VALUES 
(1, 'sandbox', 
 'APP_USR-693539317469614-093015-187360732ada6ace5a4baff74cf821eb-1785495662',
 'APP_USR-000e0fa7-b72f-439d-81a0-ea5411d16c6e',
 'https://omnibus-guadalajara.com/vision_creativa/api/payments/webhook.php',
 1);

-- ============================================================================
-- VISTA PARA CONSULTAS AVANZADAS
-- ============================================================================

-- Vista de pedidos completos (sin DEFINER para compatibilidad)
CREATE OR REPLACE VIEW `v_pedidos_completos` AS
SELECT 
    p.id, p.id_usuario, p.tipo_pedido, p.id_referencia,
    p.external_reference, p.preference_id, p.payment_id,
    p.monto_total, p.shipping_cost, p.tax_amount,
    p.shipping_address_id, p.notes, p.estado_pago,
    p.payment_method, p.estado_envio, p.fecha_pedido, p.updated_at,
    u.nombre as cliente_nombre, u.email as cliente_email, u.telefono as cliente_telefono,
    d.calle, d.colonia, d.ciudad, d.estado, d.cp,
    COUNT(pi.id) as total_items,
    GROUP_CONCAT(pi.producto_nombre SEPARATOR ', ') as productos
FROM pedidos p
LEFT JOIN usuarios u ON p.id_usuario = u.id
LEFT JOIN direcciones d ON p.shipping_address_id = d.id
LEFT JOIN pedido_items pi ON p.id = pi.pedido_id
GROUP BY p.id;

-- ============================================================================
-- FINALIZACIÓN
-- ============================================================================

-- Confirmar que todo se ejecutó correctamente
SELECT 'Base de datos Vision Creativa configurada para SEMI-SANDBOX (TEST con HTTPS)' as status;
SELECT COUNT(*) as total_tablas FROM information_schema.tables 
WHERE table_schema = 'diginet_vision_creativa';

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
