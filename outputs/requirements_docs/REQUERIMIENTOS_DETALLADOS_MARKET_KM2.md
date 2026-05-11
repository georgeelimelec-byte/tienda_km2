# Requerimientos Detallados - Market KM2

## Alcance Vigente

Market KM2 es un sistema web para tienda virtual con pedidos por WhatsApp. El sistema publica productos, permite registro e inicio de sesion de clientes, recibe pedidos desde checkout web, aplica control de stock cuando esta habilitado, redirige a WhatsApp y permite que el operador atienda el pedido desde una bandeja tipo tabla.

Quedan fuera del alcance vigente: POS, caja, SUNAT, facturacion, reservas, almacenes, compras, proveedores, lotes, kardex y hardware de impresion. PECAN permanece como sistema externo oficial para venta y comprobantes.

## Requerimientos Generales

### RG01. Tienda Virtual Operativa

El sistema debe mostrar una vitrina publica con productos activos, categorias, banners, precios, precio referencial, promociones vigentes, detalle de presentaciones y disponibilidad segun el modo de stock configurado.

### RG02. Cuenta De Cliente

El cliente debe registrarse o iniciar sesion antes de completar el pedido. El checkout debe precargar nombre, WhatsApp y direccion cuando el cliente ya existe.

### RG03. Pedidos Por WhatsApp

El sistema debe convertir el carrito en un pedido WhatsApp, guardar cabecera y detalle, calcular delivery y total referencial, y generar un enlace `wa.me` con el resumen para continuar la atencion fuera del sistema.

### RG04. Stock

El sistema controla `presentaciones_producto.stock` como el unico stock del alcance vigente. La configuracion `configuracion_tienda.control_stock_habilitado` define el comportamiento:

- Con control de stock habilitado, el checkout valida stock, descuenta al crear pedido, devuelve al cancelar y ajusta diferencias por cambios operativos.
- Con control de stock deshabilitado, la tienda funciona como catalogo: permite registrar pedidos aunque el stock este en cero y no descuenta ni devuelve unidades.

No se aprueba separar stock real, stock web ni otros saldos paralelos dentro del sistema.

### RG05. Administracion Interna

El panel administrativo debe gestionar productos, categorias, promociones, banners, zonas de delivery, pedidos, usuarios, roles, permisos, reportes, auditoria y configuracion comercial.

### RG06. Seguridad Y Auditoria

Las acciones relevantes deben registrar usuario, rol, accion, entidad, descripcion legible, valores antes/despues, IP, dispositivo y fecha. El administrador debe poder revisar la auditoria desde el panel.

## Requerimientos Funcionales

### RF01. Usuarios, Roles Y Permisos

- Permitir inicio y cierre de sesion administrativo.
- Crear, editar, activar, inactivar y eliminar usuarios internos.
- Administrar roles: `Admin General`, `Administrador`, `Atencion WhatsApp` y `Operador WhatsApp`.
- Administrar permisos por modulo: `Pedidos`, `Catalogo`, `Tienda Virtual`, `Reportes`, `Configuracion` y `Usuarios`.
- Evitar que el sistema quede sin usuarios administrativos validos.

### RF02. Clientes

- Registrar cliente con nombre, correo, WhatsApp, direccion y contrasena.
- Permitir inicio y cierre de sesion de cliente.
- Precargar datos del cliente en checkout.
- Asociar carrito y trazabilidad de pedidos por datos del cliente.

### RF03. Catalogo Tecnico

- Crear y editar productos.
- Crear presentaciones con codigo de barras, unidad, costo, precio, precio referencial, stock y stock minimo.
- Cargar imagenes de productos y presentaciones.
- Administrar categorias y subcategorias.
- Evitar eliminaciones que rompan relaciones con pedidos.

### RF04. Promociones

- Crear promociones por porcentaje o monto.
- Aplicar promociones a productos especificos.
- Aplicar promociones a categorias completas.
- Mantener fechas de vigencia y estado.
- Calcular el mejor precio promocional sin modificar el precio base del producto.

### RF05. Tienda Publica

- Mostrar productos activos con presentaciones activas.
- Permitir busqueda por texto y filtrado por categoria.
- Mostrar banners activos y promociones vigentes.
- Mostrar detalle de producto, variantes y productos relacionados.
- Mostrar stock disponible solo como condicion de compra cuando el control de stock este habilitado.
- Permitir agregar productos con stock cero cuando el control de stock este deshabilitado y el sistema opere como catalogo.

### RF06. Carrito Y Checkout

- Agregar, actualizar y retirar productos del carrito.
- Recalcular precios y promociones en servidor durante checkout.
- Validar stock en servidor solamente cuando el control de stock este habilitado.
- Aceptar pedidos con stock cero cuando el control de stock este deshabilitado.
- Validar sesion de cliente, direccion y zona de delivery.
- Crear `pedidos_tienda` y `detalle_pedidos_tienda`.
- Guardar `cantidad_solicitada`, `cantidad_confirmada`, `motivo_ajuste` y `estado_item`.
- Redirigir al cliente a WhatsApp con el resumen.

### RF07. Bandeja De Pedidos

- Mostrar pedidos en tabla operativa.
- Estados vigentes: `Pendiente`, `Observado`, `Ajustado`, `Confirmado`, `En Preparacion`, `En Delivery`, `Entregado`, `Cancelado`.
- Actualizar estado desde el panel.
- Ajustar cantidades confirmadas por item cuando el operador confirme una cantidad distinta a la solicitada.
- Devolver stock al cancelar un pedido solamente cuando el control de stock este habilitado.
- Ajustar stock por diferencias de cantidad solamente cuando el control de stock este habilitado.
- Ver detalle y ticket operativo del pedido.

### RF08. Zonas De Delivery

- Crear y editar zonas con tarifa, cobertura y estado.
- Usar la tarifa de la zona en el total referencial del pedido.
- Permitir recojo en tienda como zona con tarifa cero.

### RF09. Auditoria

- Registrar acciones de pedidos, ajustes, productos, promociones y movimientos de stock.
- Mostrar auditoria en una vista administrativa.
- Mostrar movimientos de stock con stock anterior y nuevo cuando el control de stock genere movimientos.

### RF11. Configuracion De Tienda

- Permitir activar o desactivar el control de stock desde configuracion.
- Mantener el control de stock habilitado por defecto.
- Cuando el control se desactive, comunicar el modo catalogo en la tienda y evitar bloqueos por stock.

### RF10. Reportes Y Analitica

- Consultar pedidos por rango de fechas.
- Calcular ingresos estimados, pedidos pendientes, productos vendidos y zonas con demanda.
- Exportar reporte CSV de pedidos WhatsApp.
- Mostrar tendencia diaria y mix de estados.

## Modelo De Datos Vigente

Tablas principales:

- `clientes_web`
- `carrito_items`
- `productos`
- `presentaciones_producto`
- `imagenes_producto`
- `categorias_producto`
- `unidades_medida`
- `promociones`
- `promociones_productos`
- `promociones_categorias`
- `pedidos_tienda`
- `detalle_pedidos_tienda`
- `movimientos_stock`
- `auditoria_sistema`
- `banners_tienda`
- `zonas_entrega`
- `configuracion_tienda`
- `usuarios_internos`
- `roles_sistema`
- `modulos_sistema`
- `permisos_por_rol`
- `permisos_por_usuario`
- `personal_access_tokens`
- `sessions`
- `cache`
- `cache_locks`
- `migrations`

Campos clave del pedido:

- `presentaciones_producto.stock` conserva el stock unico del sistema por presentacion.
- `presentaciones_producto.stock_minimo` conserva el umbral referencial de bajo stock.
- `configuracion_tienda.control_stock_habilitado` define si el pedido valida/descuenta stock o si opera en modo catalogo.
- `pedidos_tienda.referencia_atencion` guarda una referencia interna de pago o atencion por WhatsApp. No representa boleta, factura ni comprobante SUNAT.
- `detalle_pedidos_tienda.cantidad_solicitada` conserva lo pedido por el cliente.
- `detalle_pedidos_tienda.cantidad_confirmada` conserva lo validado por el operador.
- `detalle_pedidos_tienda.motivo_ajuste` explica cualquier cambio de cantidad.

## Criterios De Aceptacion

- Una instalacion nueva no crea tablas legacy de POS, caja, reservas, almacenes, compras, proveedores, lotes ni kardex.
- `php artisan migrate` debe limpiar una base existente con tablas legacy.
- La base de datos debe coincidir con los requerimientos vigentes; no debe contener tablas sin uso funcional.
- Con `control_stock_habilitado` activo, el pedido debe validar y descontar stock.
- Con `control_stock_habilitado` inactivo, el pedido debe aceptarse aunque el stock este en cero y no debe generar movimientos de descuento.
- `php artisan test` debe pasar.
- `php artisan route:list` no debe exponer rutas POS, reservas, almacenes, compras o caja.
