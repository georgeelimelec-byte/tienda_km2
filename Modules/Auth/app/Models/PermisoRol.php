<?php

namespace Modules\Auth\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo Eloquent para la tabla 'permisos_por_rol' (PK compuesta).
 *
 * @property int $id_rol
 * @property int $id_modulo
 * @property bool $leer
 * @property bool $crear
 * @property bool $editar
 * @property bool $eliminar
 */
class PermisoRol extends Model
{
    protected $table = 'permisos_por_rol';
    public $incrementing = false;
    public $timestamps = false;

    protected $primaryKey = ['id_rol', 'id_modulo'];

    protected $fillable = ['id_rol', 'id_modulo', 'leer', 'crear', 'editar', 'eliminar'];

    protected $casts = [
        'leer' => 'boolean',
        'crear' => 'boolean',
        'editar' => 'boolean',
        'eliminar' => 'boolean',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class, 'id_rol', 'id_rol');
    }

    public function modulo()
    {
        return $this->belongsTo(Modulo::class, 'id_modulo', 'id_modulo');
    }
}
