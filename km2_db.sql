-- Market KM2 - esquema vigente
-- Alcance: tienda virtual y pedidos WhatsApp.
-- Generado desde la base actual. No incluye datos sensibles.
SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `banners_web`;
CREATE TABLE `banners_web` (
  `id_banner` int unsigned NOT NULL AUTO_INCREMENT,
  `titulo` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `imagen_url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `link_destino` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `posicion` enum('Carrusel','Lateral','Pop_up') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Carrusel',
  `estado` enum('Activo','Inactivo') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Activo',
  PRIMARY KEY (`id_banner`)
) ENGINE=InnoDB AUTO_INCREMENT=92 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `cache`;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `cache_locks`;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `carrito_compras_web`;
CREATE TABLE `carrito_compras_web` (
  `id_carrito` int unsigned NOT NULL AUTO_INCREMENT,
  `id_cliente` int unsigned NOT NULL,
  `id_presentacion` int unsigned NOT NULL,
  `cantidad` int unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_carrito`),
  UNIQUE KEY `carrito_cliente_presentacion_unique` (`id_cliente`,`id_presentacion`),
  KEY `carrito_compras_web_id_presentacion_foreign` (`id_presentacion`),
  CONSTRAINT `carrito_compras_web_id_cliente_foreign` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `carrito_compras_web_id_presentacion_foreign` FOREIGN KEY (`id_presentacion`) REFERENCES `productos_presentaciones` (`id_presentacion`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `categorias`;
CREATE TABLE `categorias` (
  `id_categoria` int unsigned NOT NULL AUTO_INCREMENT,
  `id_categoria_padre` int unsigned DEFAULT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `estado` enum('Activo','Inactivo') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Activo',
  PRIMARY KEY (`id_categoria`),
  KEY `categorias_id_categoria_padre_foreign` (`id_categoria_padre`),
  CONSTRAINT `categorias_id_categoria_padre_foreign` FOREIGN KEY (`id_categoria_padre`) REFERENCES `categorias` (`id_categoria`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `clientes`;
CREATE TABLE `clientes` (
  `id_cliente` int unsigned NOT NULL AUTO_INCREMENT,
  `tipo_documento` enum('DNI','RUC','CE','Sin Documento') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Sin Documento',
  `numero_documento` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nombre_o_razon_social` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `direccion` text COLLATE utf8mb4_unicode_ci,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `celular` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_cliente`),
  UNIQUE KEY `clientes_celular_unique` (`celular`),
  KEY `idx_documento` (`numero_documento`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `empresa_configuracion`;
CREATE TABLE `empresa_configuracion` (
  `id_empresa` int unsigned NOT NULL AUTO_INCREMENT,
  `ruc` varchar(11) COLLATE utf8mb4_unicode_ci NOT NULL,
  `razon_social` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre_comercial` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `logo_url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'logo_default.png',
  `direccion_fiscal` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `telefono_contacto` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `correo_contacto` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ubigeo` varchar(6) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `porcentaje_igv` decimal(5,2) NOT NULL DEFAULT '18.00',
  `moneda` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'PEN',
  `horario_atencion` varchar(160) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mensaje_operativo` text COLLATE utf8mb4_unicode_ci,
  `estado` enum('Activo','Inactivo') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Activo',
  PRIMARY KEY (`id_empresa`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `modulos`;
CREATE TABLE `modulos` (
  `id_modulo` int unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` enum('Activo','Inactivo') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Activo',
  PRIMARY KEY (`id_modulo`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `pedidos_whatsapp`;
CREATE TABLE `pedidos_whatsapp` (
  `id_pedido_whatsapp` int unsigned NOT NULL AUTO_INCREMENT,
  `codigo_pedido` varchar(24) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cliente_nombre` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cliente_whatsapp` varchar(24) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cliente_direccion` text COLLATE utf8mb4_unicode_ci,
  `cliente_referencia` text COLLATE utf8mb4_unicode_ci,
  `id_zona_delivery` int unsigned DEFAULT NULL,
  `total_productos` decimal(12,2) NOT NULL DEFAULT '0.00',
  `costo_delivery` decimal(12,2) NOT NULL DEFAULT '0.00',
  `total_pedido` decimal(12,2) NOT NULL DEFAULT '0.00',
  `estado` enum('Pendiente','Confirmado','En Preparacion','En Reparto','Entregado','Cancelado') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pendiente',
  `whatsapp_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `comprobante_referencia` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nota_interna` text COLLATE utf8mb4_unicode_ci,
  `id_operador` int unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_pedido_whatsapp`),
  UNIQUE KEY `pedidos_whatsapp_codigo_pedido_unique` (`codigo_pedido`),
  KEY `pedidos_whatsapp_id_zona_delivery_foreign` (`id_zona_delivery`),
  KEY `pedidos_whatsapp_id_operador_foreign` (`id_operador`),
  CONSTRAINT `pedidos_whatsapp_id_operador_foreign` FOREIGN KEY (`id_operador`) REFERENCES `usuarios` (`id_usuario`) ON DELETE SET NULL,
  CONSTRAINT `pedidos_whatsapp_id_zona_delivery_foreign` FOREIGN KEY (`id_zona_delivery`) REFERENCES `zonas_delivery` (`id_zona`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `pedidos_whatsapp_detalles`;
CREATE TABLE `pedidos_whatsapp_detalles` (
  `id_detalle` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_pedido_whatsapp` int unsigned NOT NULL,
  `id_producto` int unsigned NOT NULL,
  `id_presentacion` int unsigned DEFAULT NULL,
  `nombre_producto` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `precio_unitario` decimal(12,2) NOT NULL,
  `cantidad` int NOT NULL,
  `subtotal` decimal(12,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_detalle`),
  KEY `pedidos_whatsapp_detalles_id_pedido_whatsapp_foreign` (`id_pedido_whatsapp`),
  KEY `pedidos_whatsapp_detalles_id_producto_foreign` (`id_producto`),
  KEY `pedidos_whatsapp_detalles_id_presentacion_foreign` (`id_presentacion`),
  CONSTRAINT `pedidos_whatsapp_detalles_id_pedido_whatsapp_foreign` FOREIGN KEY (`id_pedido_whatsapp`) REFERENCES `pedidos_whatsapp` (`id_pedido_whatsapp`) ON DELETE CASCADE,
  CONSTRAINT `pedidos_whatsapp_detalles_id_presentacion_foreign` FOREIGN KEY (`id_presentacion`) REFERENCES `productos_presentaciones` (`id_presentacion`) ON DELETE SET NULL,
  CONSTRAINT `pedidos_whatsapp_detalles_id_producto_foreign` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `permisos_rol`;
CREATE TABLE `permisos_rol` (
  `id_rol` int unsigned NOT NULL,
  `id_modulo` int unsigned NOT NULL,
  `leer` tinyint(1) NOT NULL DEFAULT '0',
  `crear` tinyint(1) NOT NULL DEFAULT '0',
  `editar` tinyint(1) NOT NULL DEFAULT '0',
  `eliminar` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_rol`,`id_modulo`),
  KEY `permisos_rol_id_modulo_foreign` (`id_modulo`),
  CONSTRAINT `permisos_rol_id_modulo_foreign` FOREIGN KEY (`id_modulo`) REFERENCES `modulos` (`id_modulo`) ON DELETE CASCADE,
  CONSTRAINT `permisos_rol_id_rol_foreign` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id_rol`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `permisos_usuario`;
CREATE TABLE `permisos_usuario` (
  `id_usuario` int unsigned NOT NULL,
  `id_modulo` int unsigned NOT NULL,
  `leer` tinyint(1) DEFAULT NULL,
  `crear` tinyint(1) DEFAULT NULL,
  `editar` tinyint(1) DEFAULT NULL,
  `eliminar` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id_usuario`,`id_modulo`),
  KEY `permisos_usuario_id_modulo_foreign` (`id_modulo`),
  CONSTRAINT `permisos_usuario_id_modulo_foreign` FOREIGN KEY (`id_modulo`) REFERENCES `modulos` (`id_modulo`) ON DELETE CASCADE,
  CONSTRAINT `permisos_usuario_id_usuario_foreign` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `personal_access_tokens`;
CREATE TABLE `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `productos`;
CREATE TABLE `productos` (
  `id_producto` int unsigned NOT NULL AUTO_INCREMENT,
  `id_categoria` int unsigned NOT NULL,
  `nombre_base` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `imagen_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` enum('Activo','Inactivo') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Activo',
  PRIMARY KEY (`id_producto`),
  KEY `productos_id_categoria_foreign` (`id_categoria`),
  CONSTRAINT `productos_id_categoria_foreign` FOREIGN KEY (`id_categoria`) REFERENCES `categorias` (`id_categoria`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `productos_imagenes`;
CREATE TABLE `productos_imagenes` (
  `id_imagen` int unsigned NOT NULL AUTO_INCREMENT,
  `id_producto` int unsigned NOT NULL,
  `id_presentacion` int unsigned DEFAULT NULL,
  `imagen_url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `orden` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_imagen`),
  KEY `productos_imagenes_id_producto_foreign` (`id_producto`),
  KEY `productos_imagenes_presentacion_idx` (`id_presentacion`),
  CONSTRAINT `productos_imagenes_id_producto_foreign` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`) ON DELETE CASCADE,
  CONSTRAINT `productos_imagenes_presentacion_fk` FOREIGN KEY (`id_presentacion`) REFERENCES `productos_presentaciones` (`id_presentacion`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=88 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `productos_presentaciones`;
CREATE TABLE `productos_presentaciones` (
  `id_presentacion` int unsigned NOT NULL AUTO_INCREMENT,
  `id_producto` int unsigned NOT NULL,
  `id_unidad` int unsigned NOT NULL DEFAULT '1',
  `nombre_variante` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `codigo_barras` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `costo_compra` decimal(10,2) NOT NULL DEFAULT '0.00',
  `precio` decimal(10,2) NOT NULL,
  `precio_oferta` decimal(10,2) DEFAULT NULL,
  `stock` int NOT NULL DEFAULT '0',
  `stock_minimo` int NOT NULL DEFAULT '5',
  `estado` enum('Activo','Inactivo') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Activo',
  PRIMARY KEY (`id_presentacion`),
  UNIQUE KEY `productos_presentaciones_codigo_barras_unique` (`codigo_barras`),
  KEY `productos_presentaciones_id_producto_foreign` (`id_producto`),
  KEY `productos_presentaciones_id_unidad_foreign` (`id_unidad`),
  KEY `idx_barras` (`codigo_barras`),
  CONSTRAINT `productos_presentaciones_id_producto_foreign` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`) ON DELETE RESTRICT,
  CONSTRAINT `productos_presentaciones_id_unidad_foreign` FOREIGN KEY (`id_unidad`) REFERENCES `unidades_medida` (`id_unidad`)
) ENGINE=InnoDB AUTO_INCREMENT=55 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `resenas`;
CREATE TABLE `resenas` (
  `id_resena` int unsigned NOT NULL AUTO_INCREMENT,
  `id_producto` int unsigned NOT NULL,
  `id_cliente` int unsigned NOT NULL,
  `calificacion` tinyint NOT NULL,
  `comentario` text COLLATE utf8mb4_unicode_ci,
  `estado` enum('Pendiente','Aprobado','Oculto') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Aprobado',
  `fecha_creacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_resena`),
  KEY `resenas_id_producto_foreign` (`id_producto`),
  KEY `resenas_id_cliente_foreign` (`id_cliente`),
  CONSTRAINT `resenas_id_cliente_foreign` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`) ON DELETE CASCADE,
  CONSTRAINT `resenas_id_producto_foreign` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
  `id_rol` int unsigned NOT NULL AUTO_INCREMENT,
  `nombre_rol` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nivel_acceso` int NOT NULL,
  `estado` enum('Activo','Inactivo') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Activo',
  PRIMARY KEY (`id_rol`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `storefront_settings`;
CREATE TABLE `storefront_settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `store_name` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Market KM2',
  `store_tagline` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Minimarket & Cafe',
  `logo_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `primary_color` varchar(7) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#f97316',
  `primary_light_color` varchar(7) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#fb923c',
  `primary_dark_color` varchar(7) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#ea580c',
  `accent_color` varchar(7) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#1f2937',
  `header_style` enum('solid','dark') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'solid',
  `card_style` enum('rounded','compact','flat') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'rounded',
  `show_login_link` tinyint(1) NOT NULL DEFAULT '1',
  `footer_text` varchar(160) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `unidades_medida`;
CREATE TABLE `unidades_medida` (
  `id_unidad` int unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abreviatura` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `estado` enum('Activo','Inactivo') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Activo',
  PRIMARY KEY (`id_unidad`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE `usuarios` (
  `id_usuario` int unsigned NOT NULL AUTO_INCREMENT,
  `id_rol` int unsigned NOT NULL,
  `nombres` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `foto_url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'default-user.png',
  `estado` enum('Activo','Inactivo') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Activo',
  `fecha_registro` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_usuario`),
  UNIQUE KEY `usuarios_email_unique` (`email`),
  KEY `usuarios_id_rol_foreign` (`id_rol`),
  CONSTRAINT `usuarios_id_rol_foreign` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id_rol`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `zonas_delivery`;
CREATE TABLE `zonas_delivery` (
  `id_zona` int unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tarifa` decimal(10,2) NOT NULL DEFAULT '0.00',
  `estado` enum('Activo','Inactivo') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Activo',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_zona`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS=1;
