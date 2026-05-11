<?php

namespace Modules\Auth\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo Eloquent para la tabla 'roles_sistema'.
 *
 * @property int $id_rol
 * @property string $nombre_rol
 * @property int $nivel_acceso
 * @property string $estado
 */
class Role extends Model
{
    protected $table = 'roles_sistema';
    protected $primaryKey = 'id_rol';
    public $timestamps = false;

    protected $fillable = ['nombre_rol', 'nivel_acceso', 'estado'];

    public function usuarios()
    {
        return $this->hasMany(Usuario::class, 'id_rol', 'id_rol');
    }

    public function permisos()
    {
        return $this->hasMany(PermisoRol::class, 'id_rol', 'id_rol');
    }
}
