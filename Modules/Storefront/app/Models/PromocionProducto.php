<?php

namespace Modules\Storefront\Models;

use Illuminate\Database\Eloquent\Model;

class PromocionProducto extends Model
{
    protected $table = 'promocion_productos';
    protected $primaryKey = 'id_promocion_producto';
    public $timestamps = false;

    protected $fillable = ['id_promocion', 'id_producto'];
}
