# Arquitectura - Market KM2

Market KM2 usa una arquitectura monolitica modular sobre Laravel 11. La aplicacion se organiza en tres modulos activos:

- `Auth`: administracion, autenticacion interna, usuarios, roles, permisos, configuracion, reportes y analitica.
- `Inventory`: catalogo tecnico, productos, categorias, presentaciones, imagenes, precios, precio referencial y stock web.
- `Storefront`: tienda publica, cuenta de cliente, carrito, checkout, pedidos WhatsApp, promociones, banners, zonas de delivery, auditoria y APIs.

No hay modulos activos de POS, reservas, almacenes, compras, caja, SUNAT ni hardware. PECAN queda fuera de integracion y se usa como sistema externo real para comprobantes y stock oficial.
El ticket generado por Market KM2 es operativo y referencial; no reemplaza boleta, factura ni comprobante fiscal.

## Diagrama General

```mermaid
flowchart LR
    Cliente["Cliente web"] --> Cuenta["Login / registro"]
    Cuenta --> Tienda["Tienda virtual"]
    Tienda --> Carrito["Carrito"]
    Carrito --> Checkout["Checkout"]
    Checkout --> Pedido["pedidos_whatsapp"]
    Checkout --> Stock["Reserva stock_web"]
    Pedido --> Detalle["pedidos_whatsapp_detalles"]
    Pedido --> WhatsApp["Redireccion wa.me"]

    Admin["Usuario interno"] --> Panel["Panel administrativo"]
    Panel --> Pedidos["Tabla de pedidos"]
    Panel --> Ajustes["Ajustes de cantidades"]
    Ajustes --> StockMov["stock_web_movimientos"]
    Panel --> Promos["Promociones"]
    Panel --> Auditoria["Auditoria operativa"]
    Panel --> Reportes["Reportes y analitica"]
```

## Capas

```mermaid
flowchart TB
    UI["Blade, Livewire y API"] --> Routes["Rutas y middleware"]
    Routes --> Controllers["Controladores"]
    Controllers --> Services["Servicios: precios, stock web, auditoria"]
    Services --> Models["Modelos Eloquent"]
    Models --> DB["Base de datos relacional"]
    Controllers --> Whatsapp["WhatsApp wa.me"]
```

## Modelo De Datos

```mermaid
erDiagram
    CLIENTES ||--o{ CARRITOS_WEB : tiene
    CLIENTES ||--o{ PEDIDOS_WHATSAPP : genera_por_datos
    PEDIDOS_WHATSAPP ||--o{ PEDIDOS_WHATSAPP_DETALLES : contiene
    PRODUCTOS ||--o{ PRODUCTOS_PRESENTACIONES : define
    PRODUCTOS_PRESENTACIONES ||--o{ PRODUCTOS_IMAGENES : muestra
    PRODUCTOS_PRESENTACIONES ||--o{ PEDIDOS_WHATSAPP_DETALLES : solicitado_como
    PRODUCTOS_PRESENTACIONES ||--o{ STOCK_WEB_MOVIMIENTOS : registra
    PRODUCTOS ||--o{ PROMOCION_PRODUCTOS : participa
    CATEGORIAS ||--o{ PROMOCION_CATEGORIAS : participa
    PROMOCIONES ||--o{ PROMOCION_PRODUCTOS : aplica
    PROMOCIONES ||--o{ PROMOCION_CATEGORIAS : aplica
    CATEGORIAS ||--o{ PRODUCTOS : agrupa
    ZONAS_DELIVERY ||--o{ PEDIDOS_WHATSAPP : tarifa
    USUARIOS ||--o{ PEDIDOS_WHATSAPP : atiende
    USUARIOS ||--o{ AUDITORIA_OPERATIVA : ejecuta
```

## Flujo De Pedido

```mermaid
sequenceDiagram
    participant C as Cliente
    participant S as Storefront
    participant DB as Base de datos
    participant W as WhatsApp
    participant A as Operador

    C->>S: Inicia sesion o se registra
    C->>S: Agrega productos al carrito
    C->>S: Completa checkout
    S->>DB: Recalcula precios, promociones y stock_web
    S->>DB: Crea pedido y detalles
    S->>DB: Descuenta stock_web
    S->>W: Redirige con resumen
    A->>S: Abre tabla de pedidos
    A->>DB: Ajusta cantidades si PECAN no coincide
    A->>DB: Actualiza estado y auditoria
```

Los detalles del pedido almacenan `cantidad_solicitada` y `cantidad_confirmada`. No se mantiene una tercera cantidad operacional para evitar duplicidad en la base de datos.

## Rutas Principales

- `/`: tienda publica.
- `/producto/{id}`: detalle de producto.
- `/cliente/login`: ingreso de cliente.
- `/cliente/registro`: registro de cliente.
- `/checkout`: checkout web autenticado como cliente.
- `/admin/pedidos`: tabla operativa de pedidos WhatsApp.
- `/admin/promociones`: gestion de promociones.
- `/admin/auditoria`: auditoria y movimientos de stock web.
- `/admin/productos`: catalogo comercial.
- `/admin/inventory/products`: catalogo tecnico.
- `/admin/reportes`: reportes.
- `/admin/business-data`: analitica.
- `/admin/configuracion`: configuracion.
- `/admin/usuarios`, `/admin/roles`, `/admin/permisos`: seguridad.
