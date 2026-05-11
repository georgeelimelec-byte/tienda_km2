# Requerimientos Detallados - Market KM2

## Alcance Vigente

Market KM2 es un sistema web para tienda virtual con pedidos por WhatsApp. El sistema publica productos, permite registro e inicio de sesion de clientes, recibe pedidos desde checkout web, aplica control de stock cuando esta habilitado, redirige a WhatsApp y permite que el operador atienda el pedido desde una bandeja tipo tabla.

Quedan fuera del alcance vigente: POS, caja, SUNAT, facturacion, reservas, almacenes, compras, proveedores, lotes, kardex y hardware de impresion. PECAN permanece como sistema externo oficial para venta y comprobantes.

## Requerimientos Generales

### RG01. Tienda Virtual Operativa

El sistema debe mostrar una vitrina publica con productos activos, categorias, banners, precios, precio referencial, promociones vigentes, detalle de presentaciones y disponibilidad segun el modo de stock configurado.

### RG02. Cuenta De Cliente

El cliente debe registrarse o iniciar sesion antes de completar el pedido. El checkout debe precargar nombre, numero de WhatsApp y direccion cuando el cliente ya existe.

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

## Requerimientos Funcionales Detallados

La numeracion funcional se organiza por requerimiento general. Cada codigo `RFxx.y` puede usarse para validacion, pruebas y control de cambios.

### RF01. Tienda Virtual Operativa

| Codigo | Descripcion | Criterio de aceptacion |
| --- | --- | --- |
| RF01.1 | Mostrar una pagina publica de tienda con productos activos, categorias, imagenes, precios y presentaciones activas. | El cliente ingresa a `/` y visualiza productos publicados con precio e imagen. |
| RF01.2 | Permitir busqueda por texto y filtrado por categoria o subcategoria. | El listado se actualiza segun texto o categoria seleccionada. |
| RF01.3 | Mostrar detalle de producto con variantes, galeria, precio efectivo, precio referencial y productos relacionados. | La ruta `/producto/{id}` presenta la informacion completa del producto activo. |
| RF01.4 | Mostrar banners y promociones activas configuradas desde el panel. | Banners y promociones vigentes aparecen en la tienda publica. |
| RF01.5 | Adaptar la disponibilidad de compra segun el modo de stock configurado. | Con control activo bloquea productos sin stock; con modo catalogo permite pedir con stock cero. |

### RF02. Cuenta De Cliente

| Codigo | Descripcion | Criterio de aceptacion |
| --- | --- | --- |
| RF02.1 | Registrar clientes con nombre, correo, numero de WhatsApp, direccion y contrasena. | El cliente queda registrado en `clientes_web`. |
| RF02.2 | Permitir inicio y cierre de sesion de clientes. | La sesion se crea al autenticar y se elimina al cerrar sesion. |
| RF02.3 | Precargar en checkout los datos disponibles del cliente autenticado. | Nombre, numero de WhatsApp y direccion aparecen precargados cuando existen. |
| RF02.4 | Exigir sesion de cliente antes de finalizar pedido. | Un cliente no autenticado es redirigido al login antes de completar checkout. |

### RF03. Pedidos Por WhatsApp

| Codigo | Descripcion | Criterio de aceptacion |
| --- | --- | --- |
| RF03.1 | Permitir agregar, actualizar y retirar productos del carrito. | El carrito conserva items, cantidades y subtotales antes del checkout. |
| RF03.2 | Recalcular precios, promociones, delivery y total referencial en servidor. | El pedido usa valores recalculados desde base de datos. |
| RF03.3 | Crear cabecera y detalle de pedido WhatsApp. | Se registran datos en `pedidos_tienda` y `detalle_pedidos_tienda`. |
| RF03.4 | Guardar cantidad solicitada, cantidad confirmada, precio unitario, subtotal y estado de item. | Cada item conserva trazabilidad operativa de cantidades y subtotales. |
| RF03.5 | Generar enlace `wa.me` con el resumen del pedido. | Al completar checkout se redirige al cliente a WhatsApp con codigo, items y total referencial. |

### RF04. Stock Del Sistema

| Codigo | Descripcion | Criterio de aceptacion |
| --- | --- | --- |
| RF04.1 | Manejar un unico stock por presentacion en `presentaciones_producto.stock`. | No existen saldos funcionales paralelos como stock real o stock web. |
| RF04.2 | Permitir activar o desactivar el control de stock desde configuracion. | `configuracion_tienda.control_stock_habilitado` define el modo operativo. |
| RF04.3 | Validar disponibilidad antes de crear pedido cuando el control de stock este habilitado. | Si la cantidad supera el stock disponible, el pedido no se registra. |
| RF04.4 | Descontar stock al crear pedido y devolverlo al cancelar cuando el control este habilitado. | El stock baja al registrar pedido y retorna si se cancela. |
| RF04.5 | Ajustar stock por diferencias de cantidad confirmada cuando el control este habilitado. | Aumentos reservan stock adicional y reducciones devuelven unidades. |
| RF04.6 | Operar como catalogo cuando el control de stock este deshabilitado. | Se aceptan pedidos con stock cero y no se generan movimientos de descuento o devolucion. |
| RF04.7 | Registrar movimientos de stock solo cuando exista variacion real de stock. | `movimientos_stock` conserva cantidad, stock anterior, stock nuevo, motivo y usuario cuando aplica. |

### RF05. Administracion Interna

| Codigo | Descripcion | Criterio de aceptacion |
| --- | --- | --- |
| RF05.1 | Administrar productos, categorias, subcategorias, presentaciones e imagenes. | El administrador crea y edita catalogo desde las rutas administrativas. |
| RF05.2 | Administrar promociones, banners y zonas de delivery. | El administrador gestiona descuentos, piezas visibles y tarifas por zona. |
| RF05.3 | Mostrar pedidos en tabla operativa con busqueda, filtro, estados y ticket. | El operador revisa y actualiza pedidos desde `/admin/pedidos`. |
| RF05.4 | Ajustar cantidades confirmadas con motivo de ajuste. | El detalle guarda cantidad confirmada y motivo cuando difiere de lo solicitado. |
| RF05.5 | Administrar usuarios, roles y permisos internos. | Roles y permisos controlan acceso a modulos administrativos. |
| RF05.6 | Configurar datos comerciales, apariencia y modo de stock. | Los cambios impactan la tienda publica y el flujo de pedido. |

### RF06. Seguridad, Auditoria Y Reportes

| Codigo | Descripcion | Criterio de aceptacion |
| --- | --- | --- |
| RF06.1 | Proteger rutas administrativas mediante autenticacion y permisos. | Usuarios sin permiso no acceden a modulos restringidos. |
| RF06.2 | Registrar acciones relevantes de pedidos, productos, promociones y configuracion. | La auditoria guarda usuario, accion, entidad, descripcion, valores, IP y fecha. |
| RF06.3 | Mostrar auditoria y movimientos de stock desde el panel. | El administrador revisa acciones y movimientos operativos. |
| RF06.4 | Emitir reportes de pedidos, ingresos estimados, productos vendidos, estados y zonas. | Los reportes muestran indicadores y permiten exportacion CSV. |
| RF06.5 | Evitar rutas y tablas funcionales fuera del alcance aprobado. | No se publican flujos de POS, caja, SUNAT, almacenes, compras, proveedores, lotes o kardex. |

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
- `clientes_web.celular` conserva el numero de WhatsApp declarado por el cliente para contacto y precarga de checkout.
- `pedidos_tienda.cliente_whatsapp` conserva el numero de WhatsApp usado para contactar al cliente por el pedido.
- `configuracion_tienda.whatsapp_number` conserva el numero de WhatsApp de atencion del negocio para generar el enlace `wa.me`.
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
