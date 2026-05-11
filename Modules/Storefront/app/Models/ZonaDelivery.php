<?php

namespace Modules\Storefront\Models;

use Illuminate\Database\Eloquent\Model;

class ZonaDelivery extends Model
{
    protected $table = 'zonas_entrega';
    protected $primaryKey = 'id_zona';

    protected $fillable = ['nombre', 'tarifa', 'estado'];

    protected $casts = [
        'tarifa' => 'decimal:2',
    ];
}
