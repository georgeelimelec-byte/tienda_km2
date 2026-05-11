# Vistas UX - Market KM2

Indice operativo de vistas vigentes despues del refactor a tienda virtual y pedidos WhatsApp.

| Nro. | Vista | Ruta | Proposito |
|---:|---|---|---|
| 1 | Tienda virtual - catalogo | `/` | Busqueda y exploracion de productos activos. |
| 2 | Tienda virtual - detalle de producto | `/producto/{id}` | Imagenes, variantes, precio, disponibilidad segun modo de stock y productos relacionados. |
| 3 | Login cliente | `/cliente/login` | Acceso del cliente para precargar datos de pedido. |
| 4 | Registro cliente | `/cliente/registro` | Alta de cliente con WhatsApp y direccion. |
| 5 | Checkout | `/checkout` | Datos precargados del cliente, direccion, zona de delivery y envio a WhatsApp. |
| 6 | Login administrativo | `/login` | Acceso seguro al panel interno. |
| 7 | Dashboard administrativo | `/dashboard` | Resumen operativo de pedidos, catalogo, banners, zonas, reportes y usuarios. |
| 8 | Bandeja de pedidos | `/admin/pedidos` | Tabla operativa de pedidos WhatsApp, estados y ajustes de cantidades. |
| 9 | Ticket de pedido | `/admin/pedidos/{id}/ticket` | Vista imprimible/browser del pedido. |
| 10 | Productos comerciales | `/admin/productos` | Gestion de productos visibles en tienda. |
| 11 | Catalogo tecnico | `/admin/inventory/products` | Presentaciones, codigos, precios, precio referencial y stock. |
| 12 | Categorias | `/admin/categorias` | Jerarquia del catalogo. |
| 13 | Promociones | `/admin/promociones` | Reglas de descuento por productos o categorias. |
| 14 | Banners | `/admin/banners` | Ubicaciones visuales promocionales. |
| 15 | Zonas de delivery | `/admin/zonas-delivery` | Cobertura y tarifas. |
| 16 | Auditoria | `/admin/auditoria` | Acciones de usuarios y movimientos de stock. |
| 17 | Reportes | `/admin/reportes` | Pedidos, ingresos estimados, zonas y productos top. |
| 18 | Analitica | `/admin/business-data` | Tendencia, ticket promedio y mix de estados. |
| 19 | Configuracion | `/admin/configuracion` | Datos comerciales, apariencia de tienda y modo de control de stock. |
| 20 | Usuarios | `/admin/usuarios` | Cuentas internas. |
| 21 | Roles | `/admin/roles` | Roles del sistema. |
| 22 | Permisos | `/admin/permisos` | Permisos por rol y por usuario. |

## Notas

- Las capturas anteriores fueron retiradas porque pertenecian al alcance previo.
- Para regenerar capturas, iniciar la aplicacion local y capturar solamente las rutas listadas.
