# Requerimientos detallados - Market KM2

## Alcance Vigente

Market KM2 es un sistema web para tienda virtual con gestion de pedidos por WhatsApp. El sistema permite publicar productos, recibir pedidos desde checkout web, redirigir al cliente a WhatsApp y administrar el estado de cada pedido desde un panel interno.

Quedan fuera del alcance vigente: POS, caja, reservas, almacenes, compras, proveedores, lotes, kardex y hardware de impresion.

## Requerimientos Generales

### RG01. Tienda virtual operativa

El sistema debe mostrar una vitrina publica con productos activos, categorias, banners, precios, stock disponible y detalle de presentaciones.

### RG02. Pedidos por WhatsApp

El sistema debe convertir el carrito en un pedido WhatsApp, guardar cabecera y detalle, calcular delivery y total, y generar un enlace `wa.me` con el resumen para el cliente.

### RG03. Administracion interna

El sistema debe ofrecer un panel administrativo para gestionar productos, categorias, banners, zonas de delivery, pedidos, usuarios, roles, permisos, reportes y configuracion comercial.

### RG04. Stock directo

El stock se controla directamente en `productos_presentaciones.stock`. No hay distribucion por almacenes ni kardex.

### RG05. Seguridad

Las rutas administrativas requieren autenticacion. Los roles y permisos limitan acceso por modulo y accion.

### RG06. Reportes

Los reportes y el dashboard ejecutivo deben usar pedidos WhatsApp como fuente de ingresos, estados, zonas y productos vendidos.

## Requerimientos Funcionales

### RF01. Usuarios, roles y permisos

- Permitir inicio y cierre de sesion administrativo.
- Crear, editar, activar, inactivar y eliminar usuarios internos.
- Registrar nombres, correo, clave, rol, fotografia y estado.
- Administrar roles como `Admin General`, `Administrador`, `Atencion WhatsApp` y `Operador WhatsApp`.
- Administrar permisos por modulo: `Pedidos`, `Catalogo`, `Tienda Virtual`, `Reportes`, `Configuracion` y `Usuarios`.
- Evitar que el sistema quede sin usuarios administrativos validos.

### RF02. Configuracion del negocio

- Registrar RUC, razon social, nombre comercial, direccion, telefono, correo, ubigeo, moneda e IGV.
- Configurar datos visibles de la tienda virtual.
- Mantener una configuracion activa para reportes y storefront.

### RF03. Catalogo tecnico

- Crear y editar productos.
- Crear presentaciones con codigo de barras, unidad, costo, precio, oferta, stock y stock minimo.
- Cargar imagenes de productos y presentaciones.
- Administrar categorias y subcategorias.
- Evitar eliminaciones que rompan relaciones con productos o pedidos.

### RF04. Tienda publica

- Mostrar productos activos con presentaciones activas.
- Permitir busqueda por texto y filtrado por categoria.
- Mostrar banners activos.
- Mostrar detalle de producto, variantes, stock y productos relacionados.
- Exponer APIs para catalogo, categorias, banners y detalle.

### RF05. Carrito y checkout

- Agregar, actualizar y retirar productos del carrito.
- Recalcular precios y stock en servidor durante checkout.
- Validar nombre, telefono, direccion y zona de delivery.
- Crear `pedidos_whatsapp` y `pedidos_whatsapp_detalles`.
- Redirigir al cliente a WhatsApp con el resumen.

### RF06. Bandeja de pedidos

- Mostrar pedidos por estado operativo.
- Estados vigentes: `Pendiente`, `Confirmado`, `En Preparacion`, `En Reparto`, `Entregado`, `Cancelado`.
- Actualizar estado desde el panel.
- Ver detalle y ticket operativo del pedido.

### RF07. Zonas de delivery

- Crear y editar zonas con tarifa, cobertura y estado.
- Usar la tarifa de la zona en el total del pedido.
- Impedir pedidos si no hay zonas disponibles cuando el flujo requiere delivery.

### RF08. Reportes y analitica

- Consultar pedidos por rango de fechas.
- Calcular ingresos estimados, pedidos pendientes, productos vendidos y zonas con demanda.
- Exportar reporte CSV de pedidos WhatsApp.
- Mostrar tendencia diaria y mix de estados.

## Modelo De Datos Vigente

Tablas principales:

- `pedidos_whatsapp`
- `pedidos_whatsapp_detalles`
- `clientes`
- `carrito_compras_web`
- `productos`
- `productos_presentaciones`
- `productos_imagenes`
- `categorias`
- `banners_web`
- `zonas_delivery`
- `storefront_settings`
- `resenas`
- `usuarios`
- `roles`
- `modulos`
- `permisos_rol`
- `permisos_usuario`
- `empresa_configuracion`
- `personal_access_tokens`
- `sessions`
- `cache`
- `cache_locks`

## Criterios De Aceptacion

- Una instalacion nueva no crea tablas legacy de POS, caja, reservas, almacenes, compras, proveedores, lotes ni kardex.
- `php artisan migrate` debe limpiar una base existente con tablas legacy.
- `php artisan test` debe pasar.
- `php artisan route:list` no debe exponer rutas POS, reservas, almacenes, compras o caja.
