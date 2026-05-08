<?php

namespace Modules\Inventory\Models;

use Illuminate\Database\Eloquent\Model;

/** @property int $id_unidad @property string $nombre @property string $abreviatura */
class UnidadMedida extends Model
{
    protected $table = 'unidades_medida';
    protected $primaryKey = 'id_unidad';
    public $timestamps = false;
    protected $fillable = ['nombre', 'abreviatura', 'estado'];
}
