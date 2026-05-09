# Metodologia Scrum - Market KM2

## Enfoque

El proyecto se organiza con Scrum para entregar incrementos funcionales de una tienda virtual con pedidos por WhatsApp. El backlog se centra en catalogo, storefront, cuenta de cliente, checkout, stock web, promociones, bandeja de pedidos, auditoria, reportes, configuracion y seguridad.

Quedan excluidos del backlog actual: POS, caja, SUNAT, boletas, facturas, reservas, almacenes, compras, proveedores, lotes, kardex y hardware.

## Roles

| Rol Scrum | Responsable | Funcion |
|---|---|---|
| Product Owner | Representante del negocio | Prioriza el alcance de tienda y pedidos WhatsApp. |
| Scrum Master | Responsable de gestion | Ordena ceremonias, bloqueos y seguimiento. |
| Development Team | Equipo tecnico | Implementa, prueba y documenta incrementos. |
| Stakeholders | Administrador, operador WhatsApp y cliente | Validan flujos de tienda, pedido y atencion. |

## Product Backlog

Epicas vigentes:

- Acceso, usuarios, roles y permisos.
- Configuracion comercial.
- Catalogo tecnico de productos y stock web.
- Cuenta de cliente.
- Tienda virtual publica.
- Promociones por productos o categorias.
- Carrito y checkout con reserva de stock web.
- Pedidos por WhatsApp.
- Auditoria operativa.
- Delivery y zonas.
- Reportes y analitica.
- APIs de storefront.

## Plan De Sprints

### Sprint 1. Base administrativa

Objetivo: habilitar login, usuarios, roles, permisos y configuracion comercial.

Entregable: panel administrativo protegido y datos base del negocio.

### Sprint 2. Catalogo tecnico

Objetivo: administrar categorias, productos, presentaciones, imagenes, precios, precio referencial y stock web.

Entregable: catalogo interno listo para publicar productos.

### Sprint 3. Tienda virtual

Objetivo: publicar catalogo, banners, busqueda, detalle de producto y zonas de delivery.

Entregable: storefront navegable para clientes.

### Sprint 4. Cuenta de cliente, checkout y pedidos WhatsApp

Objetivo: registrar clientes, convertir carrito en pedido, reservar stock web, guardar cabecera/detalle y enviar el resumen a WhatsApp.

Entregable: flujo completo desde tienda hasta WhatsApp.

### Sprint 5. Operacion interna y auditoria

Objetivo: gestionar pedidos en tabla operativa, ajustar cantidades, devolver stock al cancelar, generar tickets y registrar auditoria.

Entregable: bandeja administrativa de pedidos y trazabilidad operativa.

### Sprint 6. Reportes y analitica

Objetivo: medir pedidos, ingresos estimados, zonas, productos vendidos y estados.

Entregable: reportes CSV y dashboard ejecutivo.

## Definition Of Done

- Rutas y vistas conectadas al alcance vigente.
- Migraciones ejecutan en base existente y en instalacion fresca.
- Seeders dejan roles, modulos y permisos alineados al flujo WhatsApp.
- Tests automatizados pasan.
- Documentacion no menciona modulos excluidos salvo en migraciones de limpieza.
