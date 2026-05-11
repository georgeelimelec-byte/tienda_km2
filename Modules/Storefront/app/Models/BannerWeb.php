<?php

namespace Modules\Storefront\Models;

use Illuminate\Database\Eloquent\Model;

class BannerWeb extends Model
{
    protected $table = 'banners_tienda';
    protected $primaryKey = 'id_banner';
    public $timestamps = false;
    protected $fillable = ['titulo', 'imagen_url', 'link_destino', 'posicion', 'estado'];
}
