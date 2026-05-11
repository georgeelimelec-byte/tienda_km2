<?php

namespace Modules\Storefront\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Inventory\Models\Producto;
use Modules\Inventory\Models\ProductoPresentacion;

class PedidoWhatsappDetalle extends Model
{
    protected $table = 'detalle_pedidos_tienda';
    protected $primaryKey = 'id_detalle';

    protected $fillable = [
        'id_pedido_whatsapp',
        'id_producto',
        'id_presentacion',
        'nombre_producto',
        'precio_unitario',
        'cantidad_solicitada',
        'cantidad_confirmada',
        'subtotal',
        'motivo_ajuste',
        'estado_item',
    ];

    protected $casts = [
        'precio_unitario' => 'decimal:2',
        'cantidad_solicitada' => 'integer',
        'cantidad_confirmada' => 'integer',
        'subtotal' => 'decimal:2',
    ];

    public function pedido()
    {
        return $this->belongsTo(PedidoWhatsapp::class, 'id_pedido_whatsapp', 'id_pedido_whatsapp');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto', 'id_producto');
    }

    public function presentacion()
    {
        return $this->belongsTo(ProductoPresentacion::class, 'id_presentacion', 'id_presentacion');
    }
}
