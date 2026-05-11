<?php

namespace Modules\Storefront\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Auth\Models\Usuario;

class AuditoriaOperativa extends Model
{
    protected $table = 'auditoria_sistema';
    protected $primaryKey = 'id_auditoria';

    protected $fillable = [
        'id_usuario',
        'rol',
        'accion',
        'entidad',
        'entidad_id',
        'descripcion',
        'valor_anterior',
        'valor_nuevo',
        'ip',
        'dispositivo',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }
}
