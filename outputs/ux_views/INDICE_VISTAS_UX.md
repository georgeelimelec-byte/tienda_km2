# Vistas UX - Market KM2

Indice operativo de vistas vigentes despues del refactor a tienda virtual y pedidos WhatsApp.

| Nro. | Vista | Ruta | Proposito |
|---:|---|---|---|
| 1 | Tienda virtual - catalogo | `/` | Busqueda y exploracion de productos activos. |
| 2 | Tienda virtual - detalle de producto | `/producto/{id}` | Imagenes, variantes, precio, stock y productos relacionados. |
| 3 | Checkout | `/checkout` | Datos del cliente, direccion, zona de delivery y envio a WhatsApp. |
| 4 | Login administrativo | `/login` | Acceso seguro al panel interno. |
| 5 | Dashboard administrativo | `/dashboard` | Resumen operativo de pedidos, catalogo, banners, zonas, reportes y usuarios. |
| 6 | Bandeja de pedidos | `/admin/pedidos` | Gestion por estado de pedidos WhatsApp. |
| 7 | Ticket de pedido | `/admin/pedidos/{id}/ticket` | Vista imprimible/browser del pedido. |
| 8 | Productos comerciales | `/admin/productos` | Gestion de productos visibles en tienda. |
| 9 | Catalogo tecnico | `/admin/inventory/products` | Presentaciones, codigos, precios y stock directo. |
| 10 | Categorias | `/admin/categorias` | Jerarquia del catalogo. |
| 11 | Banners | `/admin/banners` | Promociones y ubicaciones visuales. |
| 12 | Zonas de delivery | `/admin/zonas-delivery` | Cobertura y tarifas. |
| 13 | Reportes | `/admin/reportes` | Pedidos, ingresos estimados, zonas y productos top. |
| 14 | Analitica | `/admin/business-data` | Tendencia, ticket promedio y mix de estados. |
| 15 | Configuracion | `/admin/configuracion` | Datos comerciales y apariencia de tienda. |
| 16 | Usuarios | `/admin/usuarios` | Cuentas internas. |
| 17 | Roles | `/admin/roles` | Roles del sistema. |
| 18 | Permisos | `/admin/permisos` | Permisos por rol y por usuario. |

## Notas

- Las capturas anteriores fueron retiradas porque pertenecian al alcance previo.
- Para regenerar capturas, iniciar la aplicacion local y capturar solamente las rutas listadas.
