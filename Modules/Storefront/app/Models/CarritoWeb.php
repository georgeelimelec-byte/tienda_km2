<?php

namespace Modules\Storefront\Models;

use Illuminate\Database\Eloquent\Model;

class CarritoWeb extends Model
{
    protected $table = 'carritos_web';
    protected $primaryKey = 'id_carrito';
    public $timestamps = false;
    protected $fillable = ['id_cliente', 'id_presentacion', 'cantidad'];

    public function cliente() { return $this->belongsTo(Cliente::class, 'id_cliente', 'id_cliente'); }
    public function presentacion() { return $this->belongsTo(\Modules\Inventory\Models\ProductoPresentacion::class, 'id_presentacion', 'id_presentacion'); }
}
