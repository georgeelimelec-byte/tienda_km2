# Arquitectura - Market KM2

Market KM2 usa una arquitectura monolitica modular sobre Laravel 11. La aplicacion se organiza en tres modulos activos:

- `Auth`: administracion, autenticacion interna, usuarios, roles, permisos, configuracion, reportes y analitica.
- `Inventory`: catalogo tecnico, productos, categorias, presentaciones, imagenes, precios, precio referencial y stock.
- `Storefront`: tienda publica, cuenta de cliente, carrito, checkout, pedidos WhatsApp, promociones, banners, zonas de delivery, auditoria y APIs.

No hay modulos activos de POS, reservas, almacenes, compras, caja, SUNAT ni hardware. PECAN queda fuera de integracion y se usa como sistema externo para comprobantes oficiales.
El ticket generado por Market KM2 es operativo y referencial; no reemplaza boleta, factura ni comprobante fiscal.

El sistema mantiene un solo stock por presentacion (`presentaciones_producto.stock`). La configuracion `configuracion_tienda.control_stock_habilitado` determina si el checkout valida y descuenta stock o si la tienda opera como catalogo, permitiendo pedidos con stock cero sin generar movimientos de stock.

## Diagrama General

```mermaid
flowchart LR
    Cliente["Cliente web"] --> Cuenta["Login / registro"]
    Cuenta --> Tienda["Tienda virtual"]
    Tienda --> Carrito["Carrito"]
    Carrito --> Checkout["Checkout"]
    Checkout --> ConfigStock["control_stock_habilitado"]
    Checkout --> Pedido["pedidos_tienda"]
    ConfigStock --> Stock["Valida / reserva stock si aplica"]
    Pedido --> Detalle["detalle_pedidos_tienda"]
    Pedido --> WhatsApp["Redireccion wa.me"]

    Admin["Usuario interno"] --> Panel["Panel administrativo"]
    Panel --> Pedidos["Tabla de pedidos"]
    Panel --> Ajustes["Ajustes de cantidades"]
    Ajustes --> StockMov["movimientos_stock"]
    Panel --> Promos["Promociones"]
    Panel --> Auditoria["Auditoria sistema"]
    Panel --> Reportes["Reportes y analitica"]
```

## Capas

```mermaid
flowchart TB
    UI["Blade, Livewire y API"] --> Routes["Rutas y middleware"]
    Routes --> Controllers["Controladores"]
    Controllers --> Services["Servicios: precios, stock, auditoria"]
    Services --> Models["Modelos Eloquent"]
    Models --> DB["Base de datos relacional"]
    Controllers --> Whatsapp["WhatsApp wa.me"]
```

## Modelo De Datos

```mermaid
erDiagram
    CLIENTES_WEB ||--o{ CARRITO_ITEMS : tiene
    CLIENTES_WEB ||--o{ PEDIDOS_TIENDA : genera_por_datos
    PEDIDOS_TIENDA ||--o{ DETALLE_PEDIDOS_TIENDA : contiene
    PRODUCTOS ||--o{ PRESENTACIONES_PRODUCTO : define
    PRESENTACIONES_PRODUCTO ||--o{ IMAGENES_PRODUCTO : muestra
    PRESENTACIONES_PRODUCTO ||--o{ DETALLE_PEDIDOS_TIENDA : solicitado_como
    PRESENTACIONES_PRODUCTO ||--o{ MOVIMIENTOS_STOCK : registra
    PRODUCTOS ||--o{ PROMOCIONES_PRODUCTOS : participa
    CATEGORIAS_PRODUCTO ||--o{ PROMOCIONES_CATEGORIAS : participa
    PROMOCIONES ||--o{ PROMOCIONES_PRODUCTOS : aplica
    PROMOCIONES ||--o{ PROMOCIONES_CATEGORIAS : aplica
    CATEGORIAS_PRODUCTO ||--o{ PRODUCTOS : agrupa
    ZONAS_ENTREGA ||--o{ PEDIDOS_TIENDA : tarifa
    USUARIOS_INTERNOS ||--o{ PEDIDOS_TIENDA : atiende
    USUARIOS_INTERNOS ||--o{ AUDITORIA_SISTEMA : ejecuta
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
    S->>DB: Recalcula precios y promociones
    S->>DB: Lee control_stock_habilitado
    S->>DB: Crea pedido y detalles
    alt Control de stock habilitado
        S->>DB: Valida y descuenta stock
    else Modo catalogo
        S->>DB: Registra pedido sin mover stock
    end
    S->>W: Redirige con resumen
    A->>S: Abre tabla de pedidos
    A->>DB: Ajusta cantidades confirmadas
    A->>DB: Actualiza estado y auditoria
```

Los detalles del pedido almacenan `cantidad_solicitada` y `cantidad_confirmada`. No se mantiene una tercera cantidad operacional ni un stock separado para evitar duplicidad en la base de datos.

## Rutas Principales

- `/`: tienda publica.
- `/producto/{id}`: detalle de producto.
- `/cliente/login`: ingreso de cliente.
- `/cliente/registro`: registro de cliente.
- `/checkout`: checkout web autenticado como cliente.
- `/admin/pedidos`: tabla operativa de pedidos WhatsApp.
- `/admin/promociones`: gestion de promociones.
- `/admin/auditoria`: auditoria y movimientos de stock.
- `/admin/productos`: catalogo comercial.
- `/admin/inventory/products`: catalogo tecnico.
- `/admin/reportes`: reportes.
- `/admin/business-data`: analitica.
- `/admin/configuracion`: configuracion.
- `/admin/usuarios`, `/admin/roles`, `/admin/permisos`: seguridad.
