<?php

namespace Modules\Auth\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo Eloquent para la tabla 'modulos_sistema'.
 *
 * @property int $id_modulo
 * @property string $nombre
 * @property string|null $descripcion
 * @property string $estado
 */
class Modulo extends Model
{
    protected $table = 'modulos_sistema';
    protected $primaryKey = 'id_modulo';
    public $timestamps = false;

    protected $fillable = ['nombre', 'descripcion', 'estado'];
}
