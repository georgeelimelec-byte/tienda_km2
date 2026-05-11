<?php

namespace Modules\Storefront\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Auth\Models\Usuario;
use Modules\Inventory\Models\ProductoPresentacion;

class StockMovimiento extends Model
{
    protected $table = 'movimientos_stock';
    protected $primaryKey = 'id_movimiento';

    protected $fillable = [
        'id_presentacion',
        'id_pedido_whatsapp',
        'tipo_movimiento',
        'cantidad',
        'stock_anterior',
        'stock_nuevo',
        'motivo',
        'id_usuario',
    ];

    public function presentacion()
    {
        return $this->belongsTo(ProductoPresentacion::class, 'id_presentacion', 'id_presentacion');
    }

    public function pedido()
    {
        return $this->belongsTo(PedidoWhatsapp::class, 'id_pedido_whatsapp', 'id_pedido_whatsapp');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }
}
