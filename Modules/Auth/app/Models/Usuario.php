<?php

namespace Modules\Auth\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

/**
 * Modelo Eloquent para la tabla 'usuarios'.
 *
 * Extiende Authenticatable para compatibilidad con el sistema de auth de Laravel.
 * Usa HasApiTokens de Sanctum para autenticación API de la tienda virtual.
 *
 * @property int $id_usuario
 * @property int $id_rol
 * @property string $nombres
 * @property string|null $email
 * @property string $password_hash
 * @property string $foto_url
 * @property string $estado
 * @property \Carbon\Carbon $fecha_registro
 */
class Usuario extends Authenticatable
{
    use HasApiTokens;

    protected $table = 'usuarios';
    protected $primaryKey = 'id_usuario';
    public $timestamps = false;

    protected $fillable = [
        'id_rol',
        'nombres',
        'email',
        'password_hash',
        'foto_url',
        'estado',
    ];

    protected $hidden = [
        'password_hash',
    ];

    /**
     * Columna usada por Laravel como "password" para autenticación.
     */
    public function getAuthPassword(): string
    {
        return $this->password_hash;
    }

    // ──────────── Relaciones ────────────

    public function role()
    {
        return $this->belongsTo(Role::class, 'id_rol', 'id_rol');
    }

    public function permisosUsuario()
    {
        return $this->hasMany(PermisoUsuario::class, 'id_usuario', 'id_usuario');
    }

    public function pedidosWhatsapp()
    {
        return $this->hasMany(\Modules\Storefront\Models\PedidoWhatsapp::class, 'id_operador', 'id_usuario');
    }
}
