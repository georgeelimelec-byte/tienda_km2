<?php

namespace Modules\Storefront\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Auth\Models\Usuario;

class PedidoWhatsapp extends Model
{
    protected $table = 'pedidos_whatsapp';
    protected $primaryKey = 'id_pedido_whatsapp';

    protected $fillable = [
        'codigo_pedido',
        'cliente_nombre',
        'cliente_whatsapp',
        'cliente_direccion',
        'cliente_referencia',
        'id_zona_delivery',
        'total_productos',
        'costo_delivery',
        'total_pedido',
        'estado',
        'whatsapp_url',
        'referencia_atencion',
        'nota_interna',
        'id_operador',
    ];

    protected $casts = [
        'total_productos' => 'decimal:2',
        'costo_delivery' => 'decimal:2',
        'total_pedido' => 'decimal:2',
    ];

    public function detalles()
    {
        return $this->hasMany(PedidoWhatsappDetalle::class, 'id_pedido_whatsapp', 'id_pedido_whatsapp');
    }

    public function zonaDelivery()
    {
        return $this->belongsTo(ZonaDelivery::class, 'id_zona_delivery', 'id_zona');
    }

    public function operador()
    {
        return $this->belongsTo(Usuario::class, 'id_operador', 'id_usuario');
    }
}
