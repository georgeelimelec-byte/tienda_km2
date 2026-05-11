<?php

namespace Modules\Inventory\Models;

use Illuminate\Database\Eloquent\Model;

class ProductoImagen extends Model
{
    protected $table = 'imagenes_producto';
    protected $primaryKey = 'id_imagen';
    public $timestamps = false;
    protected $fillable = ['id_producto', 'id_presentacion', 'imagen_url', 'orden'];

    public function producto() { return $this->belongsTo(Producto::class, 'id_producto', 'id_producto'); }

    public function presentacion()
    {
        return $this->belongsTo(ProductoPresentacion::class, 'id_presentacion', 'id_presentacion');
    }

    public function getUrlAttribute(): string
    {
        return Producto::normalizeImageUrl($this->imagen_url);
    }
}
