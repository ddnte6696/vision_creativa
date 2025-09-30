-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 30-09-2025 a las 21:19:50
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `diginet_vision_creativa`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `atributos_color`
--

CREATE TABLE `atributos_color` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `codigo_hex` varchar(7) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `atributos_color`
--

INSERT INTO `atributos_color` (`id`, `nombre`, `codigo_hex`) VALUES
(10, 'Verde Esmeralda', '#2ECC71'),
(11, 'Rojo Intenso', '#E74C3C'),
(12, 'Azul Zafiro', '#3498DB'),
(13, 'Vino Borgoña', '#943126'),
(14, 'Naranja Vital', '#F39C12'),
(15, 'Negro Clásico', '#1C1C1C'),
(16, 'Gris Grafito', '#5D6D7E'),
(17, 'Blanco Nieve', '#FDFEFE');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `atributos_spec`
--

CREATE TABLE `atributos_spec` (
  `id` int(11) NOT NULL,
  `nombre_atributo` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `atributos_spec`
--

INSERT INTO `atributos_spec` (`id`, `nombre_atributo`) VALUES
(8, 'Cierres'),
(5, 'Compartimentos'),
(2, 'Dimensiones'),
(7, 'Extras'),
(6, 'Laptop'),
(1, 'Material'),
(4, 'Peso'),
(3, 'Volumen');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `atributos_tela`
--

CREATE TABLE `atributos_tela` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `url_textura` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `atributos_tela`
--

INSERT INTO `atributos_tela` (`id`, `nombre`, `descripcion`, `url_textura`) VALUES
(1, 'Poliéster 600D', 'Un material estándar en la industria, conocido por su alta durabilidad, resistencia a la abrasión y al agua. Ideal para uso rudo y diario.', 'img/textures/polyester_600d.jpg'),
(2, 'Nylon Ripstop', 'Tejido ligero y antidesgarro que incorpora hilos de refuerzo para una mayor durabilidad. Perfecto para productos que requieren ligereza y resistencia.', 'img/textures/nylon_ripstop.jpg'),
(3, 'Lona de Algodón (Canvas)', 'Ofrece un aspecto clásico y robusto con una textura natural. Excelente para productos con un enfoque en el estilo y la estética vintage.', 'img/textures/canvas_cotton.jpg'),
(4, 'Piel Sintética (PU)', 'Una alternativa vegana al cuero que proporciona un acabado premium y elegante. Es fácil de limpiar y resistente a las manchas.', 'img/textures/pu_leather.jpg');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `carritos_abandonados`
--

CREATE TABLE `carritos_abandonados` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `preference_id` varchar(100) DEFAULT NULL,
  `external_reference` varchar(100) DEFAULT NULL,
  `cart_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`cart_data`)),
  `shipping_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`shipping_data`)),
  `total_amount` decimal(10,2) DEFAULT NULL,
  `email_sent` tinyint(1) DEFAULT 0,
  `recovered` tinyint(1) DEFAULT 0,
  `expires_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `chat_mensajes`
--

CREATE TABLE `chat_mensajes` (
  `id` int(11) NOT NULL,
  `id_proyecto_b2b` int(11) NOT NULL,
  `id_remitente` int(11) NOT NULL,
  `mensaje` text NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuracion_proyecto_b2b`
--

CREATE TABLE `configuracion_proyecto_b2b` (
  `id` int(11) NOT NULL,
  `id_proyecto_b2b` int(11) NOT NULL,
  `id_color` int(11) DEFAULT NULL,
  `id_tela` int(11) DEFAULT NULL,
  `id_diseno_base` int(11) DEFAULT NULL,
  `personalizaciones` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`personalizaciones`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contratos_b2b`
--

CREATE TABLE `contratos_b2b` (
  `id` int(11) NOT NULL,
  `id_proyecto_b2b` int(11) NOT NULL,
  `contenido_contrato` longtext NOT NULL,
  `firma_digital_cliente` text DEFAULT NULL,
  `fecha_firma` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cotizaciones_b2b`
--

CREATE TABLE `cotizaciones_b2b` (
  `id` int(11) NOT NULL,
  `id_proyecto_b2b` int(11) NOT NULL,
  `rangos_precio` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`rangos_precio`)),
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `crm_interacciones`
--

CREATE TABLE `crm_interacciones` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `tipo_interaccion` varchar(100) NOT NULL,
  `detalle` text DEFAULT NULL,
  `id_referencia_asociada` int(11) DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `direcciones`
--

CREATE TABLE `direcciones` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `calle` varchar(255) NOT NULL,
  `colonia` varchar(100) NOT NULL,
  `ciudad` varchar(100) NOT NULL,
  `estado` varchar(50) NOT NULL,
  `cp` varchar(10) NOT NULL,
  `referencias` text DEFAULT NULL,
  `is_default` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `disenos_base`
--

CREATE TABLE `disenos_base` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `url_imagen_plantilla` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `disenos_base`
--

INSERT INTO `disenos_base` (`id`, `nombre`, `descripcion`, `url_imagen_plantilla`) VALUES
(1, 'Mochila Urbana \"Commuter\"', 'Diseño moderno y funcional con compartimento acolchado para laptop, múltiples bolsillos organizadores y un perfil delgado. Ideal para la oficina y la ciudad.', 'img/designs/base_commuter.png'),
(2, 'Maletín Ejecutivo \"Legacy\"', 'Un diseño clásico y profesional para transportar documentos, laptop y accesorios. Estructura semi-rígida que proyecta elegancia y seriedad.', 'img/designs/base_legacy.png'),
(3, 'Bolsa de Viaje \"Weekender\"', 'Espaciosa y versátil, diseñada para escapadas de fin de semana. Combina un gran compartimento principal con bolsillos de fácil acceso.', 'img/designs/base_weekender.png'),
(4, 'Mochila Saco \"Sprint\"', 'Un diseño simple, ultraligero y práctico. Perfecto para eventos deportivos, promocionales o como una opción económica y de gran volumen.', 'img/designs/base_sprint.png');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mp_config`
--

CREATE TABLE `mp_config` (
  `id` int(11) NOT NULL,
  `environment` enum('sandbox','production') DEFAULT 'sandbox',
  `access_token` text NOT NULL,
  `public_key` text NOT NULL,
  `client_id` varchar(100) DEFAULT NULL,
  `client_secret` text DEFAULT NULL,
  `webhook_url` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `mp_config`
--

INSERT INTO `mp_config` (`id`, `environment`, `access_token`, `public_key`, `client_id`, `client_secret`, `webhook_url`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'sandbox', 'TEST-3907237835175069-042513-54392cff1eae5de032c47aa617dd5531-1773584073', 'TEST-23fa22ee-e058-41e0-96cc-2798229b16bc', NULL, NULL, 'https://omnibus-guadalajara.com/vision_creativa/api/payments/webhook.php', 1, '2025-09-30 18:44:44', '2025-09-30 18:50:09');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mp_notifications`
--

CREATE TABLE `mp_notifications` (
  `id` int(11) NOT NULL,
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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos`
--

CREATE TABLE `pedidos` (
  `id` int(11) NOT NULL,
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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedido_items`
--

CREATE TABLE `pedido_items` (
  `id` int(11) NOT NULL,
  `pedido_id` int(11) NOT NULL,
  `variacion_id` int(11) DEFAULT NULL,
  `producto_nombre` varchar(255) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `opciones_personalizacion` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`opciones_personalizacion`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` int(11) NOT NULL,
  `sku` varchar(100) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `precio_base` decimal(10,2) NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `origen` enum('interno','b2b_custom') NOT NULL DEFAULT 'interno',
  `id_proyecto_b2b_origen` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `sku`, `nombre`, `descripcion`, `precio_base`, `activo`, `origen`, `id_proyecto_b2b_origen`) VALUES
(1, 'VC-SCH-001', 'Mochila Escolar \"Explorer\"', 'La compañera ideal para la aventura diaria del aprendizaje. Diseñada para resistir el ritmo de la vida estudiantil, la mochila \"Explorer\" combina durabilidad, comodidad y un estilo vibrante para que cada día sea una nueva expedición.', 599.00, 1, 'interno', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `producto_specs`
--

CREATE TABLE `producto_specs` (
  `id` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `id_spec` int(11) NOT NULL,
  `valor` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `producto_specs`
--

INSERT INTO `producto_specs` (`id`, `id_producto`, `id_spec`, `valor`) VALUES
(1, 1, 1, 'Poliéster 600D de alta resistencia'),
(2, 1, 2, '45cm (alto) x 32cm (ancho) x 18cm (prof.)'),
(3, 1, 3, '25 Litros'),
(4, 1, 4, '0.6 kg'),
(5, 1, 5, '3 (Principal, frontal y para laptop)'),
(6, 1, 6, 'Acolchado, hasta 15.6 pulgadas'),
(7, 1, 7, 'Espalda ergonómica, bolsillos laterales'),
(8, 1, 8, 'Reforzados de alta durabilidad');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proyectos_b2b`
--

CREATE TABLE `proyectos_b2b` (
  `id` int(11) NOT NULL,
  `id_cliente_b2b` int(11) NOT NULL,
  `nombre_proyecto` varchar(255) NOT NULL,
  `requerimientos` text NOT NULL,
  `presupuesto` decimal(12,2) DEFAULT NULL,
  `cantidad_piezas` int(11) NOT NULL,
  `estado` enum('propuesta','cotizacion','contrato','pago_anticipo','produccion','envio','completado','cancelado') NOT NULL DEFAULT 'propuesta',
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `transaction_log`
--

CREATE TABLE `transaction_log` (
  `id` int(11) NOT NULL,
  `external_reference` varchar(100) DEFAULT NULL,
  `action` varchar(50) NOT NULL,
  `details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`details`)),
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `rol` enum('admin','b2b_client','b2c_client') NOT NULL,
  `datos_empresa` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`datos_empresa`)),
  `datos_envio` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`datos_envio`)),
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `email`, `telefono`, `password_hash`, `rol`, `datos_empresa`, `datos_envio`, `fecha_registro`) VALUES
(1, 'Moisés Cuevas Palacios', 'micuevas@gmail.com', NULL, '$2y$10$LnJYvts9vVzvADJ9sWQiU.APpYu9NPHQYMTJKG9OSiyvs/9L58KZC', 'b2c_client', NULL, '[{\"id\":\"\",\"calle\":\"Av. L\\u00e1zaro C\\u00e1rdenas 204\",\"colonia\":\"Benito Ju\\u00e1rez Norte\",\"ciudad\":\"Xalapa\",\"estado\":\"Veracruz\",\"cp\":\"91070\",\"referencias\":\"Junto a una peluquer\\u00eda.\"},{\"id\":\"addr_68c61cc3101a3\",\"calle\":\"Venustiano Carranza 14\",\"colonia\":\"Barrio Tercero\",\"ciudad\":\"Tatahuicapan de Ju\\u00e1rez\",\"estado\":\"Veracruz\",\"cp\":\"95950\",\"referencias\":\"Casa verde casi frente a una capilla de la Virgen de Guadalupe.\"}]', '2025-09-14 00:24:01'),
(2, 'Hector Manuel Garcia Escobar', 'hmge6696@gmail.com', NULL, '$2y$10$eZoKQ9yQ2jUoy1x7R9hXk.PEUo03ZTZaT4B508D5elOIGrr4pPSQ6', 'b2c_client', NULL, '[{\"id\":\"\",\"calle\":\"Av. L\\u00e1zaro C\\u00e1rdenas 204\",\"colonia\":\"Benito Ju\\u00e1rez Norte\",\"ciudad\":\"Xalapa\",\"estado\":\"Veracruz\",\"cp\":\"91070\",\"referencias\":\"Junto a una peluquer\\u00eda.\"},{\"id\":\"addr_68c61cc3101a3\",\"calle\":\"Venustiano Carranza 14\",\"colonia\":\"Barrio Tercero\",\"ciudad\":\"Tatahuicapan de Ju\\u00e1rez\",\"estado\":\"Veracruz\",\"cp\":\"95950\",\"referencias\":\"Casa verde casi frente a una capilla de la Virgen de Guadalupe.\"}]', '2025-09-25 17:03:03');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `variaciones_producto`
--

CREATE TABLE `variaciones_producto` (
  `id` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `id_color` int(11) NOT NULL,
  `url_imagen` varchar(255) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `precio_adicional` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `variaciones_producto`
--

INSERT INTO `variaciones_producto` (`id`, `id_producto`, `id_color`, `url_imagen`, `stock`, `precio_adicional`) VALUES
(1, 1, 10, 'img/mochila-escolar-verde.jpg', 50, 0.00),
(2, 1, 11, 'img/mochila-escolar-rojo.jpg', 45, 0.00),
(3, 1, 12, 'img/mochila-escolar-azul.jpg', 60, 0.00),
(4, 1, 13, 'img/mochila-escolar-vino.jpg', 30, 0.00),
(5, 1, 14, 'img/mochila-escolar-naranja.jpg', 25, 0.00),
(6, 1, 15, 'img/mochila-escolar-negro.jpg', 70, 0.00);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `v_pedidos_completos`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `v_pedidos_completos` (
`id` int(11)
,`id_usuario` int(11)
,`tipo_pedido` enum('b2b','b2c')
,`id_referencia` int(11)
,`external_reference` varchar(100)
,`preference_id` varchar(100)
,`payment_id` varchar(100)
,`monto_total` decimal(12,2)
,`shipping_cost` decimal(10,2)
,`tax_amount` decimal(10,2)
,`shipping_address_id` int(11)
,`notes` text
,`estado_pago` enum('pendiente','anticipo_pagado','pagado_completo','reembolsado','paid','failed','pending')
,`payment_method` varchar(50)
,`estado_envio` enum('preparando','enviado','entregado','cancelado')
,`fecha_pedido` timestamp
,`updated_at` timestamp
,`cliente_nombre` varchar(255)
,`cliente_email` varchar(255)
,`cliente_telefono` varchar(20)
,`calle` varchar(255)
,`colonia` varchar(100)
,`ciudad` varchar(100)
,`estado` varchar(50)
,`cp` varchar(10)
,`total_items` bigint(21)
,`productos` mediumtext
);

-- --------------------------------------------------------

--
-- Estructura para la vista `v_pedidos_completos`
--
DROP TABLE IF EXISTS `v_pedidos_completos`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_pedidos_completos`  AS SELECT `p`.`id` AS `id`, `p`.`id_usuario` AS `id_usuario`, `p`.`tipo_pedido` AS `tipo_pedido`, `p`.`id_referencia` AS `id_referencia`, `p`.`external_reference` AS `external_reference`, `p`.`preference_id` AS `preference_id`, `p`.`payment_id` AS `payment_id`, `p`.`monto_total` AS `monto_total`, `p`.`shipping_cost` AS `shipping_cost`, `p`.`tax_amount` AS `tax_amount`, `p`.`shipping_address_id` AS `shipping_address_id`, `p`.`notes` AS `notes`, `p`.`estado_pago` AS `estado_pago`, `p`.`payment_method` AS `payment_method`, `p`.`estado_envio` AS `estado_envio`, `p`.`fecha_pedido` AS `fecha_pedido`, `p`.`updated_at` AS `updated_at`, `u`.`nombre` AS `cliente_nombre`, `u`.`email` AS `cliente_email`, `u`.`telefono` AS `cliente_telefono`, `d`.`calle` AS `calle`, `d`.`colonia` AS `colonia`, `d`.`ciudad` AS `ciudad`, `d`.`estado` AS `estado`, `d`.`cp` AS `cp`, count(`pi`.`id`) AS `total_items`, group_concat(`pi`.`producto_nombre` separator ', ') AS `productos` FROM (((`pedidos` `p` left join `usuarios` `u` on(`p`.`id_usuario` = `u`.`id`)) left join `direcciones` `d` on(`p`.`shipping_address_id` = `d`.`id`)) left join `pedido_items` `pi` on(`p`.`id` = `pi`.`pedido_id`)) GROUP BY `p`.`id` ;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `atributos_color`
--
ALTER TABLE `atributos_color`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_nombre_color` (`nombre`);

--
-- Indices de la tabla `atributos_spec`
--
ALTER TABLE `atributos_spec`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_nombre_atributo` (`nombre_atributo`);

--
-- Indices de la tabla `atributos_tela`
--
ALTER TABLE `atributos_tela`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `carritos_abandonados`
--
ALTER TABLE `carritos_abandonados`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_usuario_id` (`usuario_id`),
  ADD KEY `idx_preference_id` (`preference_id`),
  ADD KEY `idx_external_reference` (`external_reference`),
  ADD KEY `idx_recovered` (`recovered`),
  ADD KEY `idx_expires_at` (`expires_at`);

--
-- Indices de la tabla `chat_mensajes`
--
ALTER TABLE `chat_mensajes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_chat_proyecto` (`id_proyecto_b2b`);

--
-- Indices de la tabla `configuracion_proyecto_b2b`
--
ALTER TABLE `configuracion_proyecto_b2b`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_id_proyecto_b2b` (`id_proyecto_b2b`),
  ADD KEY `fk_config_color` (`id_color`),
  ADD KEY `fk_config_tela` (`id_tela`),
  ADD KEY `fk_config_diseno` (`id_diseno_base`);

--
-- Indices de la tabla `contratos_b2b`
--
ALTER TABLE `contratos_b2b`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_id_proyecto_b2b_contrato` (`id_proyecto_b2b`);

--
-- Indices de la tabla `cotizaciones_b2b`
--
ALTER TABLE `cotizaciones_b2b`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_id_proyecto_b2b_cot` (`id_proyecto_b2b`);

--
-- Indices de la tabla `crm_interacciones`
--
ALTER TABLE `crm_interacciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_crm_usuario` (`id_usuario`);

--
-- Indices de la tabla `direcciones`
--
ALTER TABLE `direcciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_usuario_id` (`usuario_id`);

--
-- Indices de la tabla `disenos_base`
--
ALTER TABLE `disenos_base`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `mp_config`
--
ALTER TABLE `mp_config`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `mp_notifications`
--
ALTER TABLE `mp_notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_payment_id` (`payment_id`),
  ADD KEY `idx_external_reference` (`external_reference`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_processed` (`processed`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indices de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `external_reference` (`external_reference`),
  ADD KEY `idx_id_usuario_pedido` (`id_usuario`),
  ADD KEY `idx_external_reference` (`external_reference`),
  ADD KEY `idx_payment_id` (`payment_id`),
  ADD KEY `idx_preference_id` (`preference_id`),
  ADD KEY `shipping_address_id` (`shipping_address_id`);

--
-- Indices de la tabla `pedido_items`
--
ALTER TABLE `pedido_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_pedido_id` (`pedido_id`),
  ADD KEY `idx_variacion_id` (`variacion_id`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_sku` (`sku`),
  ADD KEY `idx_id_proyecto_b2b_origen` (`id_proyecto_b2b_origen`);

--
-- Indices de la tabla `producto_specs`
--
ALTER TABLE `producto_specs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_producto_spec` (`id_producto`,`id_spec`),
  ADD KEY `fk_spec_atributo` (`id_spec`);

--
-- Indices de la tabla `proyectos_b2b`
--
ALTER TABLE `proyectos_b2b`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_id_cliente_b2b` (`id_cliente_b2b`);

--
-- Indices de la tabla `transaction_log`
--
ALTER TABLE `transaction_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_external_reference` (`external_reference`),
  ADD KEY `idx_action` (`action`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_email` (`email`);

--
-- Indices de la tabla `variaciones_producto`
--
ALTER TABLE `variaciones_producto`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_producto_color` (`id_producto`,`id_color`),
  ADD KEY `fk_variacion_color` (`id_color`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `atributos_color`
--
ALTER TABLE `atributos_color`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de la tabla `atributos_spec`
--
ALTER TABLE `atributos_spec`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `atributos_tela`
--
ALTER TABLE `atributos_tela`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `carritos_abandonados`
--
ALTER TABLE `carritos_abandonados`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `chat_mensajes`
--
ALTER TABLE `chat_mensajes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `configuracion_proyecto_b2b`
--
ALTER TABLE `configuracion_proyecto_b2b`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `contratos_b2b`
--
ALTER TABLE `contratos_b2b`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `cotizaciones_b2b`
--
ALTER TABLE `cotizaciones_b2b`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `crm_interacciones`
--
ALTER TABLE `crm_interacciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `direcciones`
--
ALTER TABLE `direcciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `disenos_base`
--
ALTER TABLE `disenos_base`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `mp_config`
--
ALTER TABLE `mp_config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `mp_notifications`
--
ALTER TABLE `mp_notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pedido_items`
--
ALTER TABLE `pedido_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `producto_specs`
--
ALTER TABLE `producto_specs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `proyectos_b2b`
--
ALTER TABLE `proyectos_b2b`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `transaction_log`
--
ALTER TABLE `transaction_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `variaciones_producto`
--
ALTER TABLE `variaciones_producto`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `carritos_abandonados`
--
ALTER TABLE `carritos_abandonados`
  ADD CONSTRAINT `carritos_abandonados_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `configuracion_proyecto_b2b`
--
ALTER TABLE `configuracion_proyecto_b2b`
  ADD CONSTRAINT `fk_config_color` FOREIGN KEY (`id_color`) REFERENCES `atributos_color` (`id`),
  ADD CONSTRAINT `fk_config_diseno` FOREIGN KEY (`id_diseno_base`) REFERENCES `disenos_base` (`id`),
  ADD CONSTRAINT `fk_config_proyecto` FOREIGN KEY (`id_proyecto_b2b`) REFERENCES `proyectos_b2b` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_config_tela` FOREIGN KEY (`id_tela`) REFERENCES `atributos_tela` (`id`);

--
-- Filtros para la tabla `direcciones`
--
ALTER TABLE `direcciones`
  ADD CONSTRAINT `direcciones_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD CONSTRAINT `pedidos_ibfk_1` FOREIGN KEY (`shipping_address_id`) REFERENCES `direcciones` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `pedido_items`
--
ALTER TABLE `pedido_items`
  ADD CONSTRAINT `pedido_items_ibfk_1` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pedido_items_ibfk_2` FOREIGN KEY (`variacion_id`) REFERENCES `variaciones_producto` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `productos`
--
ALTER TABLE `productos`
  ADD CONSTRAINT `fk_producto_proyecto_origen` FOREIGN KEY (`id_proyecto_b2b_origen`) REFERENCES `proyectos_b2b` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `proyectos_b2b`
--
ALTER TABLE `proyectos_b2b`
  ADD CONSTRAINT `fk_proyecto_cliente` FOREIGN KEY (`id_cliente_b2b`) REFERENCES `usuarios` (`id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
