<?php

namespace Modules\Auth\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo Eloquent para la tabla 'permisos_usuario' (PK compuesta).
 * Valores NULL significan "hereda del rol".
 *
 * @property int $id_usuario
 * @property int $id_modulo
 * @property bool|null $leer
 * @property bool|null $crear
 * @property bool|null $editar
 * @property bool|null $eliminar
 */
class PermisoUsuario extends Model
{
    protected $table = 'permisos_usuario';
    public $incrementing = false;
    public $timestamps = false;

    protected $primaryKey = ['id_usuario', 'id_modulo'];

    protected $fillable = ['id_usuario', 'id_modulo', 'leer', 'crear', 'editar', 'eliminar'];

    protected $casts = [
        'leer' => 'boolean',
        'crear' => 'boolean',
        'editar' => 'boolean',
        'eliminar' => 'boolean',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }

    public function modulo()
    {
        return $this->belongsTo(Modulo::class, 'id_modulo', 'id_modulo');
    }
}
