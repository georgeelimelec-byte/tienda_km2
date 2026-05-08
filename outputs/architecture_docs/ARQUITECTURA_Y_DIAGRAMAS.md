# Arquitectura - Market KM2

Market KM2 usa una arquitectura monolitica modular sobre Laravel 11. La aplicacion se organiza en tres modulos activos:

- `Auth`: administracion, autenticacion, usuarios, roles, permisos, configuracion, reportes y analitica.
- `Inventory`: catalogo tecnico, productos, categorias, presentaciones, imagenes, precios y stock directo.
- `Storefront`: tienda publica, carrito, checkout, pedidos WhatsApp, banners, zonas de delivery y APIs.

No hay modulos activos de POS, reservas, almacenes, compras, caja ni hardware.

## Diagrama General

```mermaid
flowchart LR
    Cliente["Cliente web"] --> Tienda["Tienda virtual"]
    Tienda --> Carrito["Carrito"]
    Carrito --> Checkout["Checkout"]
    Checkout --> Pedido["pedidos_whatsapp"]
    Pedido --> Detalle["pedidos_whatsapp_detalles"]
    Checkout --> WhatsApp["Redireccion wa.me"]

    Admin["Usuario interno"] --> Panel["Panel administrativo"]
    Panel --> Pedidos["Bandeja de pedidos"]
    Panel --> Catalogo["Catalogo tecnico"]
    Panel --> Reportes["Reportes y analitica"]
    Panel --> Config["Configuracion y permisos"]
```

## Capas

```mermaid
flowchart TB
    UI["Blade, Livewire y API"] --> Routes["Rutas y middleware"]
    Routes --> Controllers["Controladores"]
    Controllers --> Domain["Modelos y servicios Laravel"]
    Domain --> DB["Base de datos relacional"]
    Domain --> Exports["CSV y vistas operativas"]
    Domain --> Whatsapp["WhatsApp"]
```

## Modelo De Datos

```mermaid
erDiagram
    CLIENTES ||--o{ CARRITOS_WEB : tiene
    CLIENTES ||--o{ PEDIDOS_WHATSAPP : genera
    PEDIDOS_WHATSAPP ||--o{ PEDIDOS_WHATSAPP_DETALLES : contiene
    PRODUCTOS ||--o{ PRODUCTOS_PRESENTACIONES : define
    PRODUCTOS_PRESENTACIONES ||--o{ PRODUCTOS_IMAGENES : muestra
    PRODUCTOS_PRESENTACIONES ||--o{ PEDIDOS_WHATSAPP_DETALLES : vendido_como
    CATEGORIAS ||--o{ PRODUCTOS : agrupa
    ZONAS_DELIVERY ||--o{ PEDIDOS_WHATSAPP : tarifa
    USUARIOS ||--o{ PEDIDOS_WHATSAPP : atiende
```

## Flujo De Pedido

```mermaid
sequenceDiagram
    participant C as Cliente
    participant S as Storefront
    participant DB as Base de datos
    participant W as WhatsApp
    participant A as Administrador

    C->>S: Agrega productos al carrito
    C->>S: Completa checkout
    S->>DB: Recalcula precios y stock
    S->>DB: Crea pedido y detalles
    S->>W: Redirige con resumen
    A->>S: Abre bandeja de pedidos
    A->>DB: Actualiza estado operativo
```

## Rutas Principales

- `/`: tienda publica.
- `/producto/{id}`: detalle de producto.
- `/checkout`: checkout web.
- `/admin/pedidos`: bandeja de pedidos WhatsApp.
- `/admin/productos`: catalogo comercial.
- `/admin/inventory/products`: catalogo tecnico.
- `/admin/reportes`: reportes.
- `/admin/business-data`: analitica.
- `/admin/configuracion`: configuracion.
- `/admin/usuarios`, `/admin/roles`, `/admin/permisos`: seguridad.
