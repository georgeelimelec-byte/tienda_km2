# Acta De Requerimientos Y Aprobacion De Alcance

**Proyecto:** Market KM2 - tienda virtual con pedidos por WhatsApp  
**Codigo de acta:** AR-MKM2-2026-05-11  
**Version:** 1.3
**Fecha:** 11 de mayo de 2026  
**Ubicacion:** Lima, Peru  
**Documento preparado para:** Revision, conformidad y firma de alcance funcional

## 1. Participantes

| Rol | Nombre | Cargo / Area | Organizacion | Firma |
| --- | --- | --- | --- | --- |
| Solicitante / Usuario responsable |  |  | Market KM2 |  |
| Aprobador funcional |  |  | Market KM2 |  |
| Responsable tecnico |  |  |  |  |
| Observador / interesado |  |  |  |  |

## 2. Objeto Del Acta

El presente documento formaliza los requerimientos, alcance funcional y criterios de aceptacion del sistema Market KM2, con el fin de dejar constancia de lo solicitado, lo incluido, lo excluido y las condiciones bajo las cuales el sistema sera presentado para validacion.

La firma de esta acta representa la conformidad de las partes respecto al alcance vigente descrito en este documento y sus anexos tecnicos.

## 3. Documentos De Referencia

Forman parte de la documentacion de soporte de esta acta:

| Documento | Ruta | Finalidad |
| --- | --- | --- |
| Requerimientos detallados | `outputs/requirements_docs/REQUERIMIENTOS_DETALLADOS_MARKET_KM2.md` | Define requerimientos generales, funcionales, modelo de datos y criterios de aceptacion. |
| Arquitectura y diagramas | `outputs/architecture_docs/ARQUITECTURA_Y_DIAGRAMAS.md` | Describe arquitectura modular, flujo principal, capas, modelo de datos y rutas principales. |
| README del proyecto | `README.md` | Resume alcance, modulos activos, flujo principal, base de datos y validacion tecnica. |

En caso de diferencia entre documentos, prevalece esta acta para efectos de alcance aprobado. Los anexos sirven como detalle tecnico y funcional complementario.

## 4. Resumen Ejecutivo

Market KM2 es una aplicacion web desarrollada sobre Laravel 11 para operar una tienda virtual con pedidos por WhatsApp. El sistema permite publicar productos, gestionar catalogo, registrar clientes, operar carrito y checkout, aplicar control de stock cuando este habilitado, generar pedidos, redirigir al cliente a WhatsApp y administrar internamente la atencion de pedidos desde un panel operativo.

El sistema se organiza en tres modulos activos:

- `Auth`: autenticacion, usuarios internos, roles, permisos, configuracion, reportes y analitica.
- `Inventory`: catalogo tecnico, categorias, productos, presentaciones, imagenes, precios, precio referencial y stock.
- `Storefront`: tienda publica, cuenta de cliente, carrito, checkout, pedidos WhatsApp, promociones, banners, zonas de delivery, auditoria y APIs.

## 5. Alcance Funcional Aprobado

### 5.1 Tienda Virtual Publica

El sistema debe permitir que el cliente navegue una vitrina publica con productos activos, categorias, subcategorias, banners, promociones vigentes, precio, precio referencial, imagenes, presentaciones y disponibilidad segun el modo de stock configurado.

Incluye:

- Pagina principal de tienda.
- Detalle de producto.
- Busqueda por texto.
- Filtro por categoria.
- Visualizacion de promociones y banners.
- Bloqueo de compra para productos sin stock solo cuando el control de stock este habilitado.
- Modo catalogo para aceptar pedidos aunque el stock este en cero cuando el control de stock este deshabilitado.

### 5.2 Registro E Inicio De Sesion De Clientes

El cliente debe poder registrarse e iniciar sesion para completar pedidos. El checkout debe precargar la informacion disponible del cliente.

Incluye:

- Registro de cliente con nombre, correo, numero de WhatsApp, direccion y contrasena.
- Inicio y cierre de sesion de cliente.
- Conservacion de sesion de cliente para checkout.
- Asociacion de pedidos a los datos declarados por el cliente.

### 5.3 Catalogo Tecnico

El sistema debe permitir la administracion de productos, categorias, presentaciones e imagenes.

Incluye:

- Alta y edicion de productos.
- Administracion de categorias y subcategorias.
- Presentaciones con unidad, codigo de barras, costo, precio, precio referencial, stock y stock minimo.
- Imagen principal y galeria.
- Control de estado activo o inactivo.

### 5.4 Stock Del Sistema

El sistema maneja un solo stock dentro del alcance aprobado: `presentaciones_producto.stock`.

Condiciones aprobadas:

- El stock se registra por presentacion de producto.
- El sistema tiene una configuracion global `control_stock_habilitado`.
- Con control de stock habilitado, el checkout valida stock desde base de datos.
- Con control de stock habilitado, al crear un pedido se descuenta stock.
- Con control de stock habilitado, al cancelar un pedido se devuelve stock.
- Con control de stock habilitado, al ajustar cantidades confirmadas se descuenta o devuelve la diferencia.
- Con control de stock deshabilitado, la tienda funciona como catalogo y permite registrar pedidos aunque el stock este en cero.
- Con control de stock deshabilitado, el sistema no descuenta ni devuelve stock por pedidos.
- Los movimientos se registran en `movimientos_stock` solo cuando existe movimiento de stock.

No se aprueba ninguna separacion o duplicidad de stock dentro del sistema, para evitar confusion operativa y documental.

### 5.5 Carrito Y Checkout

El sistema debe permitir convertir un carrito en pedido WhatsApp.

Incluye:

- Agregar, actualizar y retirar productos del carrito.
- Validar sesion de cliente antes de finalizar pedido.
- Seleccionar zona de delivery.
- Recalcular precios, promociones, delivery y stock en servidor segun el modo configurado.
- Crear `pedidos_tienda` y `detalle_pedidos_tienda`.
- Registrar cantidad solicitada y cantidad confirmada.
- Generar enlace `wa.me` con resumen del pedido.

### 5.6 Bandeja Administrativa De Pedidos

El personal interno debe gestionar pedidos desde una tabla operativa.

Incluye:

- Listado de pedidos.
- Busqueda y filtro por estado.
- Estados: `Pendiente`, `Observado`, `Ajustado`, `Confirmado`, `En Preparacion`, `En Delivery`, `Entregado`, `Cancelado`.
- Actualizacion de estado.
- Ajuste de cantidad confirmada por item.
- Motivo de ajuste.
- Recalculo de totales.
- Ticket operativo referencial.

### 5.7 Promociones, Banners Y Zonas

Incluye:

- Administracion de promociones por porcentaje o monto.
- Aplicacion de promociones a productos o categorias.
- Vigencia por fechas y estado.
- Administracion de banners de tienda.
- Administracion de zonas de delivery con tarifa y estado.
- Opcion de recojo en tienda como zona con tarifa cero.

### 5.8 Usuarios, Roles Y Permisos

Incluye:

- Inicio y cierre de sesion administrativo.
- Usuarios internos.
- Roles vigentes: `Superadministrador`, `Administrador`, `Operador`.
- Permisos por modulo.
- Proteccion para evitar dejar el sistema sin usuarios administrativos validos.

### 5.9 Auditoria Y Reportes

Incluye:

- Registro de acciones relevantes con usuario, rol, accion, entidad, descripcion, valores anteriores y nuevos, IP, dispositivo y fecha.
- Auditoria administrativa.
- Movimientos de stock.
- Reportes de pedidos, ingresos estimados, productos vendidos, estados y zonas.
- Exportacion CSV de pedidos WhatsApp.

## 6. Matriz Detallada De Requerimientos

La siguiente matriz desglosa cada requerimiento general en requerimientos funcionales firmables. Esta numeracion se usara como referencia para validacion, pruebas y control de cambios.

| Codigo | Requerimiento general | Requerimiento funcional detallado | Criterio de aceptacion |
| --- | --- | --- | --- |
| RF01.1 | RG01 Tienda virtual operativa | El sistema debe mostrar una pagina publica de tienda con productos activos, categorias, imagenes, precios y presentaciones activas. | El cliente puede ingresar a `/` y ver productos publicados con precio e imagen. |
| RF01.2 | RG01 Tienda virtual operativa | El sistema debe permitir busqueda por texto y filtrado por categoria o subcategoria. | La vitrina actualiza el listado segun texto o categoria seleccionada. |
| RF01.3 | RG01 Tienda virtual operativa | El sistema debe mostrar una vista de detalle por producto con variantes, galeria, precio efectivo, precio referencial y productos relacionados. | La ruta `/producto/{id}` muestra la informacion completa del producto activo. |
| RF01.4 | RG01 Tienda virtual operativa | El sistema debe mostrar banners y promociones activas configuradas desde el panel. | Las promociones vigentes y banners activos aparecen en la tienda publica. |
| RF01.5 | RG01 Tienda virtual operativa | El sistema debe adaptar la disponibilidad de compra segun el modo de stock configurado. | Con control activo bloquea productos sin stock; con modo catalogo permite pedir aunque el stock este en cero. |
| RF02.1 | RG02 Cuenta de cliente | El sistema debe permitir registrar clientes con nombre, correo, numero de WhatsApp, direccion y contrasena. | El cliente queda registrado en `clientes_web` y puede iniciar sesion. |
| RF02.2 | RG02 Cuenta de cliente | El sistema debe permitir inicio y cierre de sesion de clientes. | La sesion de cliente se crea al autenticar y se elimina al cerrar sesion. |
| RF02.3 | RG02 Cuenta de cliente | El checkout debe precargar los datos disponibles del cliente autenticado. | Nombre, numero de WhatsApp y direccion aparecen precargados cuando existen. |
| RF02.4 | RG02 Cuenta de cliente | El sistema debe exigir sesion de cliente antes de finalizar pedido. | Un cliente no autenticado es redirigido al login antes de completar checkout. |
| RF03.1 | RG03 Pedidos por WhatsApp | El sistema debe permitir agregar, actualizar y retirar productos del carrito. | El carrito conserva items, cantidades y subtotales antes del checkout. |
| RF03.2 | RG03 Pedidos por WhatsApp | El checkout debe recalcular precios, promociones, delivery y total referencial en servidor. | El pedido se registra usando valores recalculados desde base de datos. |
| RF03.3 | RG03 Pedidos por WhatsApp | El sistema debe crear cabecera y detalle de pedido WhatsApp. | Se registran datos en `pedidos_tienda` y `detalle_pedidos_tienda`. |
| RF03.4 | RG03 Pedidos por WhatsApp | El detalle debe guardar cantidad solicitada, cantidad confirmada, precio unitario, subtotal y estado de item. | Cada item del pedido conserva trazabilidad operativa de cantidades y subtotales. |
| RF03.5 | RG03 Pedidos por WhatsApp | El sistema debe generar enlace `wa.me` con el resumen del pedido. | Al completar el checkout se redirige al cliente a WhatsApp con codigo, items y total referencial. |
| RF04.1 | RG04 Stock | El sistema debe manejar un unico stock por presentacion en `presentaciones_producto.stock`. | No existen saldos funcionales paralelos como stock real o stock web. |
| RF04.2 | RG04 Stock | El sistema debe permitir activar o desactivar el control de stock desde configuracion. | El campo `configuracion_tienda.control_stock_habilitado` define el modo operativo. |
| RF04.3 | RG04 Stock | Con control de stock habilitado, el checkout debe validar disponibilidad antes de crear pedido. | Si la cantidad supera el stock disponible, el pedido no se registra. |
| RF04.4 | RG04 Stock | Con control de stock habilitado, el sistema debe descontar stock al crear pedido y devolverlo al cancelar. | El stock baja al registrar pedido y retorna cuando el pedido se cancela. |
| RF04.5 | RG04 Stock | Con control de stock habilitado, los ajustes de cantidad confirmada deben descontar o devolver diferencias. | Aumentos reservan stock adicional y reducciones devuelven unidades. |
| RF04.6 | RG04 Stock | Con control de stock deshabilitado, la tienda debe operar como catalogo. | Se aceptan pedidos con stock cero y no se generan movimientos de descuento o devolucion. |
| RF04.7 | RG04 Stock | Los movimientos de stock deben registrarse solo cuando exista una variacion real de stock. | `movimientos_stock` conserva cantidad, stock anterior, stock nuevo, motivo y usuario cuando aplica. |
| RF05.1 | RG05 Administracion interna | El panel debe permitir administrar productos, categorias, subcategorias, presentaciones e imagenes. | El administrador puede crear y editar catalogo desde las rutas administrativas. |
| RF05.2 | RG05 Administracion interna | El panel debe permitir administrar promociones, banners y zonas de delivery. | El administrador gestiona descuentos, piezas visibles y tarifas por zona. |
| RF05.3 | RG05 Administracion interna | El panel debe mostrar pedidos en tabla operativa con busqueda, filtro, estados y ticket. | El operador puede revisar y actualizar pedidos desde `/admin/pedidos`. |
| RF05.4 | RG05 Administracion interna | El panel debe permitir ajustar cantidades confirmadas con motivo de ajuste. | El detalle guarda la cantidad confirmada y el motivo cuando difiere de lo solicitado. |
| RF05.5 | RG05 Administracion interna | El panel debe permitir administrar usuarios, roles y permisos internos. | Los roles vigentes y permisos controlan acceso a modulos administrativos. |
| RF05.6 | RG05 Administracion interna | El panel debe permitir configurar datos comerciales, apariencia y modo de stock. | Los cambios de configuracion impactan la tienda publica y el flujo de pedido. |
| RF06.1 | RG06 Seguridad y auditoria | El sistema debe proteger rutas administrativas mediante autenticacion y permisos. | Usuarios sin permiso no acceden a modulos administrativos restringidos. |
| RF06.2 | RG06 Seguridad y auditoria | El sistema debe registrar acciones relevantes de pedidos, productos, promociones y configuracion. | La auditoria guarda usuario, accion, entidad, descripcion, valores, IP y fecha. |
| RF06.3 | RG06 Seguridad y auditoria | El sistema debe mostrar auditoria y movimientos de stock desde el panel. | El administrador puede revisar acciones y movimientos operativos. |
| RF06.4 | RG06 Seguridad y auditoria | El sistema debe emitir reportes de pedidos, ingresos estimados, productos vendidos, estados y zonas. | Los reportes muestran indicadores y permiten exportacion CSV. |
| RF06.5 | RG06 Seguridad y auditoria | El sistema no debe exponer rutas ni tablas funcionales fuera del alcance aprobado. | No se publican flujos de POS, caja, SUNAT, almacenes, compras, proveedores, lotes o kardex. |

## 7. Alcance No Incluido

Quedan expresamente fuera del alcance aprobado:

- POS.
- Caja.
- SUNAT.
- Boletas, facturas o comprobantes fiscales.
- Reservas.
- Almacenes.
- Compras.
- Proveedores.
- Lotes.
- Kardex.
- Hardware de impresion.
- Integracion automatica con PECAN.
- Conciliacion contable o tributaria.

PECAN permanece como sistema externo para venta oficial y comprobantes. Market KM2 genera pedidos y tickets operativos referenciales; no reemplaza documentos fiscales.

## 8. Modelo De Datos Principal

Tablas principales consideradas dentro del alcance:

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

Campos clave:

- `presentaciones_producto.stock`: stock unico del sistema por presentacion.
- `presentaciones_producto.stock_minimo`: umbral referencial de bajo stock.
- `configuracion_tienda.control_stock_habilitado`: define si el sistema valida/descuenta stock o si opera como catalogo.
- `clientes_web.celular`: numero de WhatsApp declarado por el cliente para contacto y precarga de checkout.
- `pedidos_tienda.cliente_whatsapp`: numero de WhatsApp usado para contactar al cliente por el pedido.
- `configuracion_tienda.whatsapp_number`: numero de WhatsApp de atencion del negocio usado para generar el enlace `wa.me`.
- `detalle_pedidos_tienda.cantidad_solicitada`: cantidad solicitada por el cliente.
- `detalle_pedidos_tienda.cantidad_confirmada`: cantidad validada por el operador.
- `detalle_pedidos_tienda.motivo_ajuste`: motivo de variacion entre cantidad solicitada y confirmada.
- `pedidos_tienda.referencia_atencion`: referencia interna de atencion o pago, sin valor fiscal.

## 9. Criterios De Aceptacion

Para considerar conforme el alcance descrito, se deben cumplir los siguientes criterios:

| Codigo | Criterio |
| --- | --- |
| CA-01 | La tienda publica muestra productos activos, categorias, imagenes, precios, promociones y disponibilidad segun configuracion. |
| CA-02 | El cliente puede registrarse con numero de WhatsApp, iniciar sesion y completar checkout. |
| CA-03 | El checkout recalcula precios en servidor y valida stock cuando el control de stock esta habilitado. |
| CA-04 | Al crear un pedido se registra cabecera y detalle en base de datos. |
| CA-05 | El pedido genera enlace de WhatsApp con resumen hacia el numero de atencion configurado. |
| CA-06 | Con control de stock habilitado, el stock se descuenta al crear pedido y se devuelve al cancelar. |
| CA-07 | Con control de stock deshabilitado, se pueden registrar pedidos con stock cero sin descontar ni devolver stock. |
| CA-08 | Los ajustes de cantidad actualizan subtotales, estado de item, auditoria y stock solo si el control esta habilitado. |
| CA-09 | La administracion permite gestionar productos, categorias, promociones, banners, zonas, pedidos, usuarios y permisos. |
| CA-10 | La auditoria registra acciones relevantes y movimientos de stock cuando apliquen. |
| CA-11 | La base de datos no contiene tablas funcionales fuera del alcance aprobado para POS, caja, almacenes, compras, proveedores, lotes o kardex. |
| CA-12 | Las pruebas automatizadas del proyecto deben ejecutarse correctamente con `php artisan test`. |
| CA-13 | Las rutas publicadas no deben exponer modulos fuera del alcance aprobado. |

## 10. Supuestos Y Restricciones

- La atencion final del cliente continua por WhatsApp.
- Los totales del pedido son referenciales para atencion operativa.
- La emision de comprobantes oficiales no forma parte del sistema Market KM2.
- La disponibilidad de compra depende del modo de stock configurado: control activo o catalogo.
- Cualquier integracion futura con sistemas externos debe ser solicitada y aprobada mediante control de cambios.

## 11. Control De Cambios

Cualquier modificacion posterior al alcance aprobado debera registrarse como solicitud de cambio, indicando:

- Codigo de solicitud.
- Descripcion del cambio.
- Justificacion.
- Impacto funcional.
- Impacto tecnico.
- Impacto en plazo y costo, si corresponde.
- Aprobacion del responsable funcional.

Los cambios no aprobados formalmente no forman parte del alcance de esta acta.

## 12. Declaracion De Conformidad

Las partes declaran haber revisado el alcance funcional, criterios de aceptacion, restricciones y documentacion anexa. Con la firma de esta acta se deja constancia de la conformidad para continuar con la presentacion, validacion o cierre de la etapa correspondiente del proyecto Market KM2.

## 13. Firmas

| Rol | Nombre | Documento / ID | Fecha | Firma |
| --- | --- | --- | --- | --- |
| Solicitante / Usuario responsable |  |  |  |  |
| Aprobador funcional |  |  |  |  |
| Responsable tecnico |  |  |  |  |
| Representante de conformidad |  |  |  |  |

## 14. Observaciones

| Nro. | Observacion | Responsable | Fecha compromiso |
| --- | --- | --- | --- |
| 1 |  |  |  |
| 2 |  |  |  |
| 3 |  |  |  |
