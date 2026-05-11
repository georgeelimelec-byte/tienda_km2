-- MySQL dump 10.13  Distrib 8.4.3, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: km2_db
-- ------------------------------------------------------
-- Server version	8.4.3

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `auditoria_sistema`
--

DROP TABLE IF EXISTS `auditoria_sistema`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `auditoria_sistema` (
  `id_auditoria` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_usuario` int unsigned DEFAULT NULL,
  `rol` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `accion` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `entidad` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `entidad_id` bigint unsigned DEFAULT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `valor_anterior` text COLLATE utf8mb4_unicode_ci,
  `valor_nuevo` text COLLATE utf8mb4_unicode_ci,
  `ip` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dispositivo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_auditoria`),
  KEY `auditoria_sistema_id_usuario_foreign` (`id_usuario`),
  CONSTRAINT `auditoria_sistema_id_usuario_foreign` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios_internos` (`id_usuario`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `auditoria_sistema`
--

LOCK TABLES `auditoria_sistema` WRITE;
/*!40000 ALTER TABLE `auditoria_sistema` DISABLE KEYS */;
/*!40000 ALTER TABLE `auditoria_sistema` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `banners_tienda`
--

DROP TABLE IF EXISTS `banners_tienda`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `banners_tienda` (
  `id_banner` int unsigned NOT NULL AUTO_INCREMENT,
  `titulo` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `imagen_url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `link_destino` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `posicion` enum('Carrusel','Lateral','Pop_up') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Carrusel',
  `estado` enum('Activo','Inactivo') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Activo',
  PRIMARY KEY (`id_banner`)
) ENGINE=InnoDB AUTO_INCREMENT=110 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `banners_tienda`
--

LOCK TABLES `banners_tienda` WRITE;
/*!40000 ALTER TABLE `banners_tienda` DISABLE KEYS */;
INSERT INTO `banners_tienda` VALUES (1,'Cafe al paso y panaderia fresca','https://images.unsplash.com/photo-1554118811-1e0d58224f24?auto=format&fit=crop&q=80&w=1800','/?categoria_id=5','Pop_up','Activo'),(2,'Promociones de despensa KM2','https://images.unsplash.com/photo-1604719312566-8912e9227c6a?auto=format&fit=crop&q=80&w=1800','/?categoria_id=2','Lateral','Activo'),(3,'Bebidas frias, snacks y recojo rapido','https://images.unsplash.com/photo-1621939514649-280e2ee25f60?auto=format&fit=crop&q=80&w=1800','/?categoria_id=3','Carrusel','Activo'),(4,'Combos de cafeteria para la oficina','https://images.unsplash.com/photo-1528735602780-2552fd46c7af?auto=format&fit=crop&q=80&w=1800','/?categoria_id=6','Carrusel','Activo');
/*!40000 ALTER TABLE `banners_tienda` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `carrito_items`
--

DROP TABLE IF EXISTS `carrito_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `carrito_items` (
  `id_carrito` int unsigned NOT NULL AUTO_INCREMENT,
  `id_cliente` int unsigned NOT NULL,
  `id_presentacion` int unsigned NOT NULL,
  `cantidad` int unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_carrito`),
  UNIQUE KEY `carrito_cliente_presentacion_unique` (`id_cliente`,`id_presentacion`),
  KEY `carrito_items_id_presentacion_foreign` (`id_presentacion`),
  CONSTRAINT `carrito_items_id_cliente_foreign` FOREIGN KEY (`id_cliente`) REFERENCES `clientes_web` (`id_cliente`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `carrito_items_id_presentacion_foreign` FOREIGN KEY (`id_presentacion`) REFERENCES `presentaciones_producto` (`id_presentacion`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `carrito_items`
--

LOCK TABLES `carrito_items` WRITE;
/*!40000 ALTER TABLE `carrito_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `carrito_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categorias_producto`
--

DROP TABLE IF EXISTS `categorias_producto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categorias_producto` (
  `id_categoria` int unsigned NOT NULL AUTO_INCREMENT,
  `id_categoria_padre` int unsigned DEFAULT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `estado` enum('Activo','Inactivo') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Activo',
  PRIMARY KEY (`id_categoria`),
  KEY `categorias_producto_id_categoria_padre_foreign` (`id_categoria_padre`),
  CONSTRAINT `categorias_producto_id_categoria_padre_foreign` FOREIGN KEY (`id_categoria_padre`) REFERENCES `categorias_producto` (`id_categoria`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categorias_producto`
--

LOCK TABLES `categorias_producto` WRITE;
/*!40000 ALTER TABLE `categorias_producto` DISABLE KEYS */;
INSERT INTO `categorias_producto` VALUES (1,NULL,'Minimarket','Activo'),(2,1,'Abarrotes y despensa','Activo'),(3,1,'Bebidas frias y snacks','Activo'),(4,NULL,'Cafeteria','Activo'),(5,4,'Cafe y bebidas calientes','Activo'),(6,4,'Sandwiches y salados','Activo'),(7,4,'Panaderia y postres','Activo'),(8,1,'Lacteos y refrigerados','Activo'),(9,1,'Cuidado y limpieza','Activo');
/*!40000 ALTER TABLE `categorias_producto` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `clientes_web`
--

DROP TABLE IF EXISTS `clientes_web`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `clientes_web` (
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
  UNIQUE KEY `clientes_web_celular_unique` (`celular`),
  KEY `idx_documento` (`numero_documento`)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clientes_web`
--

LOCK TABLES `clientes_web` WRITE;
/*!40000 ALTER TABLE `clientes_web` DISABLE KEYS */;
INSERT INTO `clientes_web` VALUES (1,'Sin Documento',NULL,'Maria Torres',NULL,'maria@example.test','51911111111',NULL,'2026-04-18 05:15:40'),(2,'Sin Documento',NULL,'Luis Ramirez',NULL,'luis@example.test','51922222222',NULL,'2026-04-18 05:15:40'),(3,'Sin Documento',NULL,'Carla Mendoza',NULL,'carla@example.test','51933333333',NULL,'2026-04-18 05:15:40');
/*!40000 ALTER TABLE `clientes_web` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `configuracion_tienda`
--

DROP TABLE IF EXISTS `configuracion_tienda`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `configuracion_tienda` (
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
  `whatsapp_number` varchar(24) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_phone` varchar(24) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_email` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `currency` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'PEN',
  `included_tax_percent` decimal(5,2) NOT NULL DEFAULT '18.00',
  `business_hours` varchar(160) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `operational_message` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `configuracion_tienda`
--

LOCK TABLES `configuracion_tienda` WRITE;
/*!40000 ALTER TABLE `configuracion_tienda` DISABLE KEYS */;
INSERT INTO `configuracion_tienda` VALUES (1,'Market KM2','Minimarket & Cafe','logo_default.png','#f9b115','#fb983c','#826955','#000000','dark','rounded',1,NULL,NULL,'999999999','ventas@marketkm2.test','SOLES',18.00,'Lunes a Domingo | 24/7',NULL,'2026-04-20 13:00:19','2026-05-10 04:24:43');
/*!40000 ALTER TABLE `configuracion_tienda` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `detalle_pedidos_tienda`
--

DROP TABLE IF EXISTS `detalle_pedidos_tienda`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `detalle_pedidos_tienda` (
  `id_detalle` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_pedido_whatsapp` int unsigned NOT NULL,
  `id_producto` int unsigned NOT NULL,
  `id_presentacion` int unsigned DEFAULT NULL,
  `nombre_producto` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `precio_unitario` decimal(12,2) NOT NULL,
  `cantidad_solicitada` int NOT NULL DEFAULT '0',
  `cantidad_confirmada` int NOT NULL DEFAULT '0',
  `subtotal` decimal(12,2) NOT NULL,
  `motivo_ajuste` text COLLATE utf8mb4_unicode_ci,
  `estado_item` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Solicitado',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_detalle`),
  KEY `detalle_pedidos_tienda_id_pedido_whatsapp_foreign` (`id_pedido_whatsapp`),
  KEY `detalle_pedidos_tienda_id_producto_foreign` (`id_producto`),
  KEY `detalle_pedidos_tienda_id_presentacion_foreign` (`id_presentacion`),
  CONSTRAINT `detalle_pedidos_tienda_id_pedido_whatsapp_foreign` FOREIGN KEY (`id_pedido_whatsapp`) REFERENCES `pedidos_tienda` (`id_pedido_whatsapp`) ON DELETE CASCADE,
  CONSTRAINT `detalle_pedidos_tienda_id_presentacion_foreign` FOREIGN KEY (`id_presentacion`) REFERENCES `presentaciones_producto` (`id_presentacion`) ON DELETE SET NULL,
  CONSTRAINT `detalle_pedidos_tienda_id_producto_foreign` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `detalle_pedidos_tienda`
--

LOCK TABLES `detalle_pedidos_tienda` WRITE;
/*!40000 ALTER TABLE `detalle_pedidos_tienda` DISABLE KEYS */;
/*!40000 ALTER TABLE `detalle_pedidos_tienda` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `imagenes_producto`
--

DROP TABLE IF EXISTS `imagenes_producto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `imagenes_producto` (
  `id_imagen` int unsigned NOT NULL AUTO_INCREMENT,
  `id_producto` int unsigned NOT NULL,
  `id_presentacion` int unsigned DEFAULT NULL,
  `imagen_url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `orden` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_imagen`),
  KEY `imagenes_producto_id_producto_foreign` (`id_producto`),
  KEY `imagenes_producto_presentacion_idx` (`id_presentacion`),
  CONSTRAINT `imagenes_producto_id_producto_foreign` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`) ON DELETE CASCADE,
  CONSTRAINT `imagenes_producto_presentacion_fk` FOREIGN KEY (`id_presentacion`) REFERENCES `presentaciones_producto` (`id_presentacion`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=100 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `imagenes_producto`
--

LOCK TABLES `imagenes_producto` WRITE;
/*!40000 ALTER TABLE `imagenes_producto` DISABLE KEYS */;
INSERT INTO `imagenes_producto` VALUES (1,1,NULL,'https://images.unsplash.com/photo-1598327105666-5b89351aff97?auto=format&fit=crop&q=80&w=900',0),(2,2,NULL,'https://images.unsplash.com/photo-1606220945770-b5b6c2c55bf1?auto=format&fit=crop&q=80&w=900',0),(3,3,NULL,'https://images.unsplash.com/photo-1583863788434-e58a36330cf0?auto=format&fit=crop&q=80&w=900',0),(4,4,NULL,'https://images.unsplash.com/photo-1615873968403-89e068629265?auto=format&fit=crop&q=80&w=900',0),(5,5,NULL,'https://images.unsplash.com/photo-1606914469633-bd39206ea739?auto=format&fit=crop&q=80&w=900',0),(6,6,NULL,'https://images.unsplash.com/photo-1626806819282-2c1dc01a5e0c?auto=format&fit=crop&q=80&w=900',0),(7,7,NULL,'https://images.unsplash.com/photo-1631729371254-42c2892f0e6e?auto=format&fit=crop&q=80&w=900',0),(8,8,NULL,'https://images.unsplash.com/photo-1509042239860-f550ce710b93?auto=format&fit=crop&q=80&w=900',0),(9,9,NULL,'https://images.unsplash.com/photo-1534778101976-62847782c213?auto=format&fit=crop&q=80&w=900',0),(10,10,NULL,'https://images.unsplash.com/photo-1528735602780-2552fd46c7af?auto=format&fit=crop&q=80&w=900',0),(11,11,NULL,'https://images.unsplash.com/photo-1555507036-ab1f4038808a?auto=format&fit=crop&q=80&w=900',0),(12,12,NULL,'https://images.unsplash.com/photo-1606313564200-e75d5e30476c?auto=format&fit=crop&q=80&w=900',0),(13,13,NULL,'https://images.unsplash.com/photo-1564419320461-6870880221ad?auto=format&fit=crop&q=80&w=900',0),(14,14,NULL,'https://images.unsplash.com/photo-1566478989037-eec170784d0b?auto=format&fit=crop&q=80&w=900',0),(15,15,NULL,'https://images.unsplash.com/photo-1586201375761-83865001e31c?auto=format&fit=crop&q=80&w=900',0),(16,16,NULL,'https://images.unsplash.com/photo-1474979266404-7eaacbcd87c5?auto=format&fit=crop&q=80&w=900',0),(17,17,NULL,'https://images.unsplash.com/photo-1571212515416-fef01fc43637?auto=format&fit=crop&q=80&w=900',0),(18,18,NULL,'https://images.unsplash.com/photo-1486297678162-eb2a19b0a32d?auto=format&fit=crop&q=80&w=900',0),(74,19,28,'http://127.0.0.1:8000/images/catalogo/presentacion_20260502235006_651816e97aba1033.webp',0),(75,19,NULL,'https://images.unsplash.com/photo-1626806819282-2c1dc01a5e0c?auto=format&fit=crop&q=80&w=900',0);
/*!40000 ALTER TABLE `imagenes_producto` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2026_04_13_000001_create_roles_table',1),(2,'2026_04_13_000002_create_modulos_table',1),(3,'2026_04_13_000003_create_usuarios_table',1),(4,'2026_04_13_000004_create_permisos_rol_table',1),(5,'2026_04_13_000005_create_permisos_usuario_table',1),(6,'2026_04_13_000010_create_categorias_table',1),(7,'2026_04_13_000011_create_unidades_medida_table',1),(9,'2026_04_13_000013_create_productos_table',1),(10,'2026_04_13_000014_create_productos_presentaciones_table',1),(11,'2026_04_13_000015_create_productos_imagenes_table',1),(16,'2026_04_13_000020_create_clientes_table',1),(20,'2026_04_13_000024_create_banners_web_table',1),(22,'2026_04_13_000027_create_zonas_delivery_table',1),(25,'2026_04_13_000050_create_shared_tables',1),(26,'2026_04_13_005348_create_sessions_table',1),(27,'2026_04_13_063244_create_cache_table',1),(29,'2026_04_20_000001_add_id_presentacion_to_productos_imagenes_table',3),(30,'2026_04_20_000002_create_storefront_settings_table',4),(43,'2026_04_13_000028_create_pedidos_whatsapp_tables',15),(47,'2026_05_06_000004_refactor_access_catalog_for_whatsapp_store',18),(49,'2026_05_07_000001_drop_unmatched_store_schema',19),(50,'2026_04_13_000025_create_carritos_web_table',20),(51,'2026_04_26_000002_add_whatsapp_operator_role',20),(52,'2026_05_06_000002_remove_legacy_user_access_field',20),(53,'2026_05_06_000003_drop_out_of_scope_operational_tables',20),(54,'2026_05_08_000001_align_whatsapp_store_database_contract',20),(55,'2026_05_08_000002_create_promotions_audit_and_stock_tables',21),(56,'2026_05_08_000003_refine_whatsapp_order_contract',22),(57,'2026_05_08_000004_drop_reviews_from_whatsapp_scope',23),(58,'2026_05_09_000001_move_store_settings_and_drop_company_table',24),(59,'2026_05_11_000001_rename_domain_tables_for_whatsapp_store',25),(60,'2026_05_11_000002_normalize_domain_index_names',26);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `modulos_sistema`
--

DROP TABLE IF EXISTS `modulos_sistema`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `modulos_sistema` (
  `id_modulo` int unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` enum('Activo','Inactivo') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Activo',
  PRIMARY KEY (`id_modulo`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `modulos_sistema`
--

LOCK TABLES `modulos_sistema` WRITE;
/*!40000 ALTER TABLE `modulos_sistema` DISABLE KEYS */;
INSERT INTO `modulos_sistema` VALUES (1,'Pedidos','Bandeja de pedidos WhatsApp y cambios de estado','Activo'),(2,'Catalogo','Productos, presentaciones, precios, fotos y stock directo','Activo'),(3,'Tienda Virtual','Banners, zonas de delivery, promociones y vitrina web','Activo'),(4,'Reportes','Metricas y exportaciones de pedidos WhatsApp','Activo'),(5,'Configuracion','Datos comerciales, apariencia y ajustes del sistema','Activo'),(6,'Usuarios','Usuarios, roles y permisos internos','Activo');
/*!40000 ALTER TABLE `modulos_sistema` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `movimientos_stock_web`
--

DROP TABLE IF EXISTS `movimientos_stock_web`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `movimientos_stock_web` (
  `id_movimiento` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_presentacion` int unsigned NOT NULL,
  `id_pedido_whatsapp` int unsigned DEFAULT NULL,
  `tipo_movimiento` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cantidad` int NOT NULL,
  `stock_anterior` int NOT NULL,
  `stock_nuevo` int NOT NULL,
  `motivo` text COLLATE utf8mb4_unicode_ci,
  `id_usuario` int unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_movimiento`),
  KEY `movimientos_stock_web_id_presentacion_foreign` (`id_presentacion`),
  KEY `movimientos_stock_web_id_pedido_whatsapp_foreign` (`id_pedido_whatsapp`),
  KEY `movimientos_stock_web_id_usuario_foreign` (`id_usuario`),
  CONSTRAINT `movimientos_stock_web_id_pedido_whatsapp_foreign` FOREIGN KEY (`id_pedido_whatsapp`) REFERENCES `pedidos_tienda` (`id_pedido_whatsapp`) ON DELETE SET NULL,
  CONSTRAINT `movimientos_stock_web_id_presentacion_foreign` FOREIGN KEY (`id_presentacion`) REFERENCES `presentaciones_producto` (`id_presentacion`) ON DELETE CASCADE,
  CONSTRAINT `movimientos_stock_web_id_usuario_foreign` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios_internos` (`id_usuario`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `movimientos_stock_web`
--

LOCK TABLES `movimientos_stock_web` WRITE;
/*!40000 ALTER TABLE `movimientos_stock_web` DISABLE KEYS */;
/*!40000 ALTER TABLE `movimientos_stock_web` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pedidos_tienda`
--

DROP TABLE IF EXISTS `pedidos_tienda`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pedidos_tienda` (
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
  `estado` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pendiente',
  `whatsapp_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `referencia_atencion` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nota_interna` text COLLATE utf8mb4_unicode_ci,
  `id_operador` int unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_pedido_whatsapp`),
  UNIQUE KEY `pedidos_tienda_codigo_pedido_unique` (`codigo_pedido`),
  KEY `pedidos_tienda_id_zona_delivery_foreign` (`id_zona_delivery`),
  KEY `pedidos_tienda_id_operador_foreign` (`id_operador`),
  CONSTRAINT `pedidos_tienda_id_operador_foreign` FOREIGN KEY (`id_operador`) REFERENCES `usuarios_internos` (`id_usuario`) ON DELETE SET NULL,
  CONSTRAINT `pedidos_tienda_id_zona_delivery_foreign` FOREIGN KEY (`id_zona_delivery`) REFERENCES `zonas_entrega` (`id_zona`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pedidos_tienda`
--

LOCK TABLES `pedidos_tienda` WRITE;
/*!40000 ALTER TABLE `pedidos_tienda` DISABLE KEYS */;
/*!40000 ALTER TABLE `pedidos_tienda` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permisos_por_rol`
--

DROP TABLE IF EXISTS `permisos_por_rol`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `permisos_por_rol` (
  `id_rol` int unsigned NOT NULL,
  `id_modulo` int unsigned NOT NULL,
  `leer` tinyint(1) NOT NULL DEFAULT '0',
  `crear` tinyint(1) NOT NULL DEFAULT '0',
  `editar` tinyint(1) NOT NULL DEFAULT '0',
  `eliminar` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_rol`,`id_modulo`),
  KEY `permisos_por_rol_id_modulo_foreign` (`id_modulo`),
  CONSTRAINT `permisos_por_rol_id_modulo_foreign` FOREIGN KEY (`id_modulo`) REFERENCES `modulos_sistema` (`id_modulo`) ON DELETE CASCADE,
  CONSTRAINT `permisos_por_rol_id_rol_foreign` FOREIGN KEY (`id_rol`) REFERENCES `roles_sistema` (`id_rol`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permisos_por_rol`
--

LOCK TABLES `permisos_por_rol` WRITE;
/*!40000 ALTER TABLE `permisos_por_rol` DISABLE KEYS */;
INSERT INTO `permisos_por_rol` VALUES (1,1,1,1,1,1),(1,2,1,1,1,1),(1,3,1,1,1,1),(1,4,1,1,1,1),(1,5,1,1,1,1),(1,6,1,1,1,1),(2,1,1,1,1,1),(2,2,1,1,1,1),(2,3,1,1,1,1),(2,4,1,1,1,1),(2,5,1,1,1,1),(2,6,1,1,1,1),(3,1,1,1,1,0),(3,2,1,0,0,0),(3,3,1,0,1,0),(3,4,1,0,0,0),(3,5,0,0,0,0),(3,6,0,0,0,0),(4,1,1,1,1,0),(4,2,1,0,0,0),(4,3,1,0,1,0),(4,4,1,0,0,0),(4,5,0,0,0,0),(4,6,0,0,0,0);
/*!40000 ALTER TABLE `permisos_por_rol` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permisos_por_usuario`
--

DROP TABLE IF EXISTS `permisos_por_usuario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `permisos_por_usuario` (
  `id_usuario` int unsigned NOT NULL,
  `id_modulo` int unsigned NOT NULL,
  `leer` tinyint(1) DEFAULT NULL,
  `crear` tinyint(1) DEFAULT NULL,
  `editar` tinyint(1) DEFAULT NULL,
  `eliminar` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id_usuario`,`id_modulo`),
  KEY `permisos_por_usuario_id_modulo_foreign` (`id_modulo`),
  CONSTRAINT `permisos_por_usuario_id_modulo_foreign` FOREIGN KEY (`id_modulo`) REFERENCES `modulos_sistema` (`id_modulo`) ON DELETE CASCADE,
  CONSTRAINT `permisos_por_usuario_id_usuario_foreign` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios_internos` (`id_usuario`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permisos_por_usuario`
--

LOCK TABLES `permisos_por_usuario` WRITE;
/*!40000 ALTER TABLE `permisos_por_usuario` DISABLE KEYS */;
/*!40000 ALTER TABLE `permisos_por_usuario` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal_access_tokens`
--

LOCK TABLES `personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `presentaciones_producto`
--

DROP TABLE IF EXISTS `presentaciones_producto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `presentaciones_producto` (
  `id_presentacion` int unsigned NOT NULL AUTO_INCREMENT,
  `id_producto` int unsigned NOT NULL,
  `id_unidad` int unsigned NOT NULL DEFAULT '1',
  `nombre_variante` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `codigo_barras` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `costo_reposicion` decimal(10,2) NOT NULL DEFAULT '0.00',
  `precio` decimal(10,2) NOT NULL,
  `precio_referencial` decimal(10,2) DEFAULT NULL,
  `stock_web` int NOT NULL DEFAULT '0',
  `stock_web_minimo` int NOT NULL DEFAULT '5',
  `estado` enum('Activo','Inactivo') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Activo',
  PRIMARY KEY (`id_presentacion`),
  UNIQUE KEY `presentaciones_producto_codigo_barras_unique` (`codigo_barras`),
  KEY `presentaciones_producto_id_producto_foreign` (`id_producto`),
  KEY `presentaciones_producto_id_unidad_foreign` (`id_unidad`),
  KEY `idx_barras` (`codigo_barras`),
  CONSTRAINT `presentaciones_producto_id_producto_foreign` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`) ON DELETE RESTRICT,
  CONSTRAINT `presentaciones_producto_id_unidad_foreign` FOREIGN KEY (`id_unidad`) REFERENCES `unidades_medida` (`id_unidad`)
) ENGINE=InnoDB AUTO_INCREMENT=85 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `presentaciones_producto`
--

LOCK TABLES `presentaciones_producto` WRITE;
/*!40000 ALTER TABLE `presentaciones_producto` DISABLE KEYS */;
INSERT INTO `presentaciones_producto` VALUES (1,1,1,'128GB Negro','KM2-A55-128-BLK',0.00,1299.00,1299.00,8,2,'Inactivo'),(2,1,1,'256GB Azul','KM2-A55-256-BLU',0.00,1599.00,NULL,5,2,'Inactivo'),(3,2,1,'Blanco','KM2-PULSE-WHT',0.00,74.90,74.90,25,2,'Inactivo'),(4,2,1,'Negro','KM2-PULSE-BLK',0.00,89.90,NULL,18,2,'Inactivo'),(5,3,1,'Unidad','KM2-CHG-25W',0.00,49.90,NULL,30,2,'Inactivo'),(6,4,1,'5 litros','KM2-AIRFRY-5L',0.00,299.90,299.90,6,2,'Inactivo'),(7,5,1,'Pack x 6','KM2-ENV-6',0.00,59.90,NULL,15,2,'Inactivo'),(8,5,1,'Pack x 10','KM2-ENV-10',0.00,79.90,79.90,10,2,'Inactivo'),(9,6,1,'Botella 3L','KM2-DETER-3L',0.00,28.90,NULL,20,2,'Inactivo'),(10,7,1,'Frasco 750ml','KM2-SHAM-750',0.00,29.90,29.90,22,2,'Inactivo'),(11,8,1,'Vaso 8 oz','KM2-CAF-AMER-8',0.00,6.50,NULL,40,3,'Activo'),(12,8,1,'Vaso 12 oz','KM2-CAF-AMER-12',0.00,8.50,NULL,32,3,'Activo'),(13,9,1,'Vaso 12 oz','KM2-CAP-12',0.00,9.90,9.90,22,3,'Activo'),(14,10,1,'Unidad','KM2-SAND-MIX',0.00,8.90,NULL,18,3,'Activo'),(15,11,1,'Unidad','KM2-CROIS-UND',0.00,5.90,NULL,24,3,'Activo'),(16,11,1,'Pack x 4','KM2-CROIS-4',0.00,19.90,19.90,8,3,'Activo'),(17,12,1,'Porcion','KM2-BROWNIE-POR',0.00,6.90,NULL,16,3,'Activo'),(18,13,1,'Botella 625 ml','KM2-AGUA-625',0.00,2.50,NULL,59,3,'Activo'),(19,13,1,'Pack x 6','KM2-AGUA-6',0.00,12.90,12.90,20,3,'Activo'),(20,14,1,'Bolsa 120 g','KM2-PAPAS-120',0.00,6.50,NULL,30,3,'Activo'),(21,15,1,'Bolsa 1 kg','KM2-ARROZ-1K',0.00,5.80,NULL,40,3,'Activo'),(22,15,1,'Bolsa 5 kg','KM2-ARROZ-5K',0.00,25.90,25.90,14,3,'Activo'),(23,16,1,'Botella 1 L','KM2-ACEITE-1L',0.00,10.90,NULL,26,3,'Activo'),(24,17,1,'Botella 1 L','KM2-YOG-FRESA-1L',0.00,8.90,NULL,18,3,'Activo'),(25,17,1,'Botella 200 ml','KM2-YOG-FRESA-200',0.00,2.80,NULL,34,3,'Activo'),(26,18,1,'Molde 500 g','KM2-QUESO-500',0.00,13.90,NULL,12,3,'Activo'),(27,19,1,'Botella 3 L','97865658',0.00,28.90,NULL,18,3,'Activo'),(28,19,1,'Botella 1 L','KM2. LIMP-DET1',0.00,9.00,NULL,10,0,'Activo');
/*!40000 ALTER TABLE `presentaciones_producto` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `productos`
--

DROP TABLE IF EXISTS `productos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `productos` (
  `id_producto` int unsigned NOT NULL AUTO_INCREMENT,
  `id_categoria` int unsigned NOT NULL,
  `nombre_base` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `imagen_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` enum('Activo','Inactivo') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Activo',
  PRIMARY KEY (`id_producto`),
  KEY `productos_id_categoria_foreign` (`id_categoria`),
  CONSTRAINT `productos_id_categoria_foreign` FOREIGN KEY (`id_categoria`) REFERENCES `categorias_producto` (`id_categoria`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `productos`
--

LOCK TABLES `productos` WRITE;
/*!40000 ALTER TABLE `productos` DISABLE KEYS */;
INSERT INTO `productos` VALUES (1,2,'Smartphone Galaxy A55','Smartphone con pantalla AMOLED, bateria de larga duracion y camara multiple para uso diario.','https://images.unsplash.com/photo-1598327105666-5b89351aff97?auto=format&fit=crop&q=80&w=900','Inactivo'),(2,3,'Audifonos Bluetooth Pulse','Audifonos inalambricos con estuche de carga, microfono integrado y autonomia para todo el dia.','https://images.unsplash.com/photo-1606220945770-b5b6c2c55bf1?auto=format&fit=crop&q=80&w=900','Inactivo'),(3,3,'Cargador USB-C 25W','Cargador compacto de carga rapida compatible con smartphones, tablets y accesorios USB-C.','https://images.unsplash.com/photo-1583863788434-e58a36330cf0?auto=format&fit=crop&q=80&w=900','Inactivo'),(4,5,'Freidora de aire 5L','Freidora de aire con canasta antiadherente, control de temperatura y capacidad familiar.','https://images.unsplash.com/photo-1615873968403-89e068629265?auto=format&fit=crop&q=80&w=900','Inactivo'),(5,5,'Set de envases hermeticos','Pack de envases resistentes para organizar alimentos secos, snacks y preparaciones.','https://images.unsplash.com/photo-1606914469633-bd39206ea739?auto=format&fit=crop&q=80&w=900','Inactivo'),(6,6,'Detergente liquido Fresh','Detergente liquido concentrado con aroma fresco para ropa blanca y de color.','https://images.unsplash.com/photo-1626806819282-2c1dc01a5e0c?auto=format&fit=crop&q=80&w=900','Inactivo'),(7,8,'Shampoo reparacion intensa','Shampoo para uso diario con formula reparadora, brillo y suavidad para el cabello.','https://images.unsplash.com/photo-1631729371254-42c2892f0e6e?auto=format&fit=crop&q=80&w=900','Inactivo'),(8,5,'Cafe americano KM2','Cafe pasado al momento, taza caliente y aroma de barra para llevar o disfrutar en tienda.','https://images.unsplash.com/photo-1509042239860-f550ce710b93?auto=format&fit=crop&q=80&w=900','Activo'),(9,5,'Capuccino artesanal','Espresso con leche vaporizada y espuma cremosa, preparado en barra de cafeteria.','https://images.unsplash.com/photo-1534778101976-62847782c213?auto=format&fit=crop&q=80&w=900','Activo'),(10,6,'Sandwich mixto caliente','Pan suave con jamon y queso fundido, servido caliente para desayuno o lonche.','https://images.unsplash.com/photo-1528735602780-2552fd46c7af?auto=format&fit=crop&q=80&w=900','Activo'),(11,7,'Croissant de mantequilla','Croissant hojaldrado de vitrina, ideal para acompanar cafe o bebida caliente.','https://images.unsplash.com/photo-1555507036-ab1f4038808a?auto=format&fit=crop&q=80&w=900','Activo'),(12,7,'Brownie de chocolate','Brownie humedo con cacao intenso, porcion individual lista para llevar.','https://images.unsplash.com/photo-1606313564200-e75d5e30476c?auto=format&fit=crop&q=80&w=900','Activo'),(13,3,'Agua mineral sin gas','Agua mineral fresca de vitrina refrigerada para acompanar compras o snacks.','https://images.unsplash.com/photo-1564419320461-6870880221ad?auto=format&fit=crop&q=80&w=900','Activo'),(14,3,'Papas nativas crocantes','Snack salado de papas nativas, bolsa lista para lonchera, oficina o camino.','https://images.unsplash.com/photo-1566478989037-eec170784d0b?auto=format&fit=crop&q=80&w=900','Activo'),(15,2,'Arroz extra seleccionado','Arroz de grano largo para despensa diaria del hogar, disponible por bolsa.','https://images.unsplash.com/photo-1586201375761-83865001e31c?auto=format&fit=crop&q=80&w=900','Activo'),(16,2,'Aceite vegetal premium','Aceite vegetal para cocina diaria, ideal para compras rapidas de minimarket.','https://images.unsplash.com/photo-1474979266404-7eaacbcd87c5?auto=format&fit=crop&q=80&w=900','Activo'),(17,8,'Yogurt bebible fresa','Yogurt refrigerado sabor fresa, listo para desayuno, snack o lonchera.','https://images.unsplash.com/photo-1571212515416-fef01fc43637?auto=format&fit=crop&q=80&w=900','Activo'),(18,8,'Queso fresco artesanal','Queso fresco de vitrina refrigerada para desayuno, sandwich o cocina casera.','https://images.unsplash.com/photo-1486297678162-eb2a19b0a32d?auto=format&fit=crop&q=80&w=900','Activo'),(19,9,'Detergente liquido multiuso','Producto esencial de limpieza para compras de reposicion del hogar.','https://images.unsplash.com/photo-1626806819282-2c1dc01a5e0c?auto=format&fit=crop&q=80&w=900','Activo');
/*!40000 ALTER TABLE `productos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `promociones`
--

DROP TABLE IF EXISTS `promociones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `promociones` (
  `id_promocion` int unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `tipo_descuento` enum('Porcentaje','Monto') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Porcentaje',
  `valor_descuento` decimal(10,2) NOT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `estado` enum('Activo','Inactivo') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Activo',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_promocion`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `promociones`
--

LOCK TABLES `promociones` WRITE;
/*!40000 ALTER TABLE `promociones` DISABLE KEYS */;
/*!40000 ALTER TABLE `promociones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `promociones_categorias`
--

DROP TABLE IF EXISTS `promociones_categorias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `promociones_categorias` (
  `id_promocion_categoria` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_promocion` int unsigned NOT NULL,
  `id_categoria` int unsigned NOT NULL,
  PRIMARY KEY (`id_promocion_categoria`),
  UNIQUE KEY `promo_categoria_unique` (`id_promocion`,`id_categoria`),
  KEY `promociones_categorias_id_categoria_foreign` (`id_categoria`),
  CONSTRAINT `promociones_categorias_id_categoria_foreign` FOREIGN KEY (`id_categoria`) REFERENCES `categorias_producto` (`id_categoria`) ON DELETE CASCADE,
  CONSTRAINT `promociones_categorias_id_promocion_foreign` FOREIGN KEY (`id_promocion`) REFERENCES `promociones` (`id_promocion`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `promociones_categorias`
--

LOCK TABLES `promociones_categorias` WRITE;
/*!40000 ALTER TABLE `promociones_categorias` DISABLE KEYS */;
/*!40000 ALTER TABLE `promociones_categorias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `promociones_productos`
--

DROP TABLE IF EXISTS `promociones_productos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `promociones_productos` (
  `id_promocion_producto` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_promocion` int unsigned NOT NULL,
  `id_producto` int unsigned NOT NULL,
  PRIMARY KEY (`id_promocion_producto`),
  UNIQUE KEY `promo_producto_unique` (`id_promocion`,`id_producto`),
  KEY `promociones_productos_id_producto_foreign` (`id_producto`),
  CONSTRAINT `promociones_productos_id_producto_foreign` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`) ON DELETE CASCADE,
  CONSTRAINT `promociones_productos_id_promocion_foreign` FOREIGN KEY (`id_promocion`) REFERENCES `promociones` (`id_promocion`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `promociones_productos`
--

LOCK TABLES `promociones_productos` WRITE;
/*!40000 ALTER TABLE `promociones_productos` DISABLE KEYS */;
/*!40000 ALTER TABLE `promociones_productos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles_sistema`
--

DROP TABLE IF EXISTS `roles_sistema`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles_sistema` (
  `id_rol` int unsigned NOT NULL AUTO_INCREMENT,
  `nombre_rol` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nivel_acceso` int NOT NULL,
  `estado` enum('Activo','Inactivo') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Activo',
  PRIMARY KEY (`id_rol`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles_sistema`
--

LOCK TABLES `roles_sistema` WRITE;
/*!40000 ALTER TABLE `roles_sistema` DISABLE KEYS */;
INSERT INTO `roles_sistema` VALUES (1,'Admin General',1,'Activo'),(2,'Administrador',2,'Activo'),(3,'Atencion WhatsApp',3,'Activo'),(4,'Operador WhatsApp',4,'Activo');
/*!40000 ALTER TABLE `roles_sistema` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('jabz5ZRaX5NqcCMqB7UOCaXPLNSC0e8doBuVLzUC',1,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','YTo0OntzOjY6Il90b2tlbiI7czo0MDoiZURNS0tsc3ppWGNtWTdGa1Z4SEpPenY2ajU2aTVQeTlKdHNCVUU5TiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9wcm9kdWN0b3MiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO30=',1778290457),('RxFPPqmtE6CoCgiywBngiMy8d5gsM17aoYrJsxU5',1,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','YTo1OntzOjY6Il90b2tlbiI7czo0MDoiYWc3RmVGanRWeDY3Q2RwMnA1NDN2YTE5UnZNbklSZzlLTzkwVGhvViI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzU6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9jbGllbnRlL2xvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czozOiJ1cmwiO2E6MDp7fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE7fQ==',1778299730);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `unidades_medida`
--

DROP TABLE IF EXISTS `unidades_medida`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `unidades_medida` (
  `id_unidad` int unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abreviatura` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `estado` enum('Activo','Inactivo') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Activo',
  PRIMARY KEY (`id_unidad`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `unidades_medida`
--

LOCK TABLES `unidades_medida` WRITE;
/*!40000 ALTER TABLE `unidades_medida` DISABLE KEYS */;
INSERT INTO `unidades_medida` VALUES (1,'Unidad','UND','Activo'),(2,'Litro','LT','Activo'),(3,'Kilogramo','KG','Activo'),(4,'Pack','PK','Activo'),(5,'Gramos','GR','Activo');
/*!40000 ALTER TABLE `unidades_medida` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuarios_internos`
--

DROP TABLE IF EXISTS `usuarios_internos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuarios_internos` (
  `id_usuario` int unsigned NOT NULL AUTO_INCREMENT,
  `id_rol` int unsigned NOT NULL,
  `nombres` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `foto_url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'default-user.png',
  `estado` enum('Activo','Inactivo') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Activo',
  `fecha_registro` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_usuario`),
  UNIQUE KEY `usuarios_internos_email_unique` (`email`),
  KEY `usuarios_internos_id_rol_foreign` (`id_rol`),
  CONSTRAINT `usuarios_internos_id_rol_foreign` FOREIGN KEY (`id_rol`) REFERENCES `roles_sistema` (`id_rol`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios_internos`
--

LOCK TABLES `usuarios_internos` WRITE;
/*!40000 ALTER TABLE `usuarios_internos` DISABLE KEYS */;
INSERT INTO `usuarios_internos` VALUES (1,1,'Super Admin','admin@ponteready.com','$2y$10$qfGiJInRCxoxaLOASRwDEuU2EgXGWhQ2m4fWMAJCSY//llaKrUemK','default-user.png','Activo','2026-04-18 05:15:40'),(2,1,'Administrador','admin@km2.com','$2y$12$UvK0WaVmUU5z9phoFoxAG.dw8L7SVz7QaikVgPCxQUEiJpXYvm./2','default-user.png','Activo','2026-04-18 02:50:07'),(3,3,'Vendedora','vendedora@localmarket.com','$2y$12$6TXGdHAvljCs4wkXzBIuDu0TT46n3HwLfbuOjpE3Y4UgLYQDWgUa6','default-user.png','Inactivo','2026-05-01 00:42:29');
/*!40000 ALTER TABLE `usuarios_internos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `zonas_entrega`
--

DROP TABLE IF EXISTS `zonas_entrega`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `zonas_entrega` (
  `id_zona` int unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tarifa` decimal(10,2) NOT NULL DEFAULT '0.00',
  `estado` enum('Activo','Inactivo') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Activo',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_zona`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `zonas_entrega`
--

LOCK TABLES `zonas_entrega` WRITE;
/*!40000 ALTER TABLE `zonas_entrega` DISABLE KEYS */;
INSERT INTO `zonas_entrega` VALUES (1,'Recojo en tienda',0.00,'Activo','2026-04-18 05:15:40','2026-04-18 05:15:40'),(2,'Fonavi',3.00,'Activo','2026-04-18 05:15:40','2026-04-20 05:09:43'),(3,'Las mercedes',3.00,'Activo','2026-04-18 05:15:40','2026-04-20 05:09:30'),(4,'Tablazo',5.00,'Activo','2026-04-18 05:15:40','2026-04-20 05:09:23'),(5,'Marco jara',5.00,'Activo','2026-04-18 05:15:40','2026-04-20 05:09:16');
/*!40000 ALTER TABLE `zonas_entrega` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-05-11 12:23:55
