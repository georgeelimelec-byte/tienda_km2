# Market KM2

Market KM2 es una aplicacion Laravel 11 para operar una tienda virtual con pedidos por WhatsApp. El alcance actual queda centrado en catalogo, carrito, checkout web, bandeja administrativa de pedidos, zonas de delivery, usuarios, permisos, reportes y analitica.

No incluye POS, reservas, almacenes, compras, proveedores, kardex, caja ni hardware de impresion.

## Modulos Activos

- `Auth`: login administrativo, usuarios, roles, permisos, configuracion, reportes y business data.
- `Inventory`: catalogo tecnico, productos, categorias, presentaciones, imagenes, precios y stock directo.
- `Storefront`: tienda publica, carrito, checkout, pedidos WhatsApp, banners, zonas de delivery y APIs.

Los modulos activos se declaran en `modules_statuses.json`.

## Flujo Principal

1. El cliente navega la tienda virtual y agrega productos al carrito.
2. El checkout recalcula precios y stock desde `productos_presentaciones`.
3. El sistema crea `pedidos_whatsapp` y `pedidos_whatsapp_detalles`.
4. El cliente es redirigido a WhatsApp con el resumen del pedido.
5. El equipo interno gestiona el pedido desde `/admin/pedidos`.
6. Reportes y analitica leen exclusivamente pedidos WhatsApp.

## Base de Datos

Tablas principales del alcance actual:

- `clientes`
- `productos`, `productos_presentaciones`, `productos_imagenes`
- `categorias`, `unidades_medida`
- `carrito_compras_web`
- `pedidos_whatsapp`, `pedidos_whatsapp_detalles`
- `banners_web`, `zonas_delivery`, `storefront_settings`, `resenas`
- `usuarios`, `roles`, `modulos`, `permisos_rol`, `permisos_usuario`
- `empresa_configuracion`
- `personal_access_tokens`, `sessions`, `cache`, `cache_locks`

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
