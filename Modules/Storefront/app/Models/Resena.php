<?php

namespace Modules\Storefront\Models;

use Illuminate\Database\Eloquent\Model;

class Resena extends Model
{
    protected $table = 'resenas';
    protected $primaryKey = 'id_resena';
    public $timestamps = false;
    protected $fillable = ['id_producto', 'id_cliente', 'calificacion', 'comentario', 'estado'];

    public function producto() { return $this->belongsTo(\Modules\Inventory\Models\Producto::class, 'id_producto', 'id_producto'); }
    public function cliente() { return $this->belongsTo(Cliente::class, 'id_cliente', 'id_cliente'); }
}
