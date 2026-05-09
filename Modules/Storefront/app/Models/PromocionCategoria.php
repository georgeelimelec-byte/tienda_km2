<?php

namespace Modules\Storefront\Models;

use Illuminate\Database\Eloquent\Model;

class PromocionCategoria extends Model
{
    protected $table = 'promocion_categorias';
    protected $primaryKey = 'id_promocion_categoria';
    public $timestamps = false;

    protected $fillable = ['id_promocion', 'id_categoria'];
}
