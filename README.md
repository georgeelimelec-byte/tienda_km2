# Market KM2

Market KM2 es una aplicacion Laravel 11 para operar una tienda virtual con pedidos por WhatsApp. El alcance actual queda centrado en catalogo, carrito, checkout web, bandeja administrativa de pedidos, zonas de delivery, usuarios, permisos, reportes y analitica.

No incluye POS, reservas, almacenes, compras, proveedores, kardex, caja, SUNAT, boletas, facturas ni hardware de impresion. PECAN queda como sistema externo para venta oficial, comprobantes y stock real.

## Modulos Activos

- `Auth`: login administrativo, usuarios, roles, permisos, configuracion, reportes y business data.
- `Inventory`: catalogo tecnico, productos, categorias, presentaciones, imagenes, precios, precio referencial y stock web.
- `Storefront`: tienda publica, cuenta de cliente, carrito, checkout, pedidos WhatsApp, promociones, banners, zonas de delivery, auditoria y APIs.

Los modulos activos se declaran en `modules_statuses.json`.

## Flujo Principal

1. El cliente navega la tienda virtual, inicia sesion o se registra, y agrega productos al carrito.
2. El checkout recalcula precios, promociones y `stock_web` desde la base de datos.
3. El sistema crea `pedidos_whatsapp` y `pedidos_whatsapp_detalles` con cantidades solicitadas y confirmadas.
4. Al crear el pedido se reserva `stock_web`; si se cancela, el stock web retorna.
5. El cliente es redirigido a WhatsApp con el resumen del pedido.
6. El equipo interno gestiona el pedido desde `/admin/pedidos` en tabla operativa, no Kanban.
7. Auditoria, reportes y analitica leen exclusivamente el flujo de pedidos WhatsApp.

## Base de Datos

Tablas principales del alcance actual:

- `clientes`
- `productos`, `productos_presentaciones`, `productos_imagenes`
- `categorias`, `unidades_medida`
- `carritos_web`
- `pedidos_whatsapp`, `pedidos_whatsapp_detalles`
- `promociones`, `promocion_productos`, `promocion_categorias`
- `auditoria_operativa`, `stock_web_movimientos`
- `banners_web`, `zonas_delivery`, `storefront_settings`
- `usuarios`, `roles`, `modulos`, `permisos_rol`, `permisos_usuario`
- `empresa_configuracion`
- `personal_access_tokens`, `sessions`, `cache`, `cache_locks`, `migrations`

Las migraciones `2026_05_06_*` y `2026_05_07_*` limpian columnas y tablas legacy o sin flujo real en instalaciones existentes.

## Instalacion Local

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm run build
php artisan serve
```

Configura el numero de la empresa para WhatsApp:

```env
WHATSAPP_EMPRESA=51999999999
```

## Validacion

```bash
composer dump-autoload --no-scripts
php artisan optimize:clear
php artisan migrate
php artisan route:list
php artisan test
```

Para validar una instalacion nueva sin tocar la base principal, usa una base SQLite temporal dentro de `storage/` y ejecuta `php artisan migrate:fresh --force`.
