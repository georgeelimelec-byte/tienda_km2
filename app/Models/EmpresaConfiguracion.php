<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmpresaConfiguracion extends Model
{
    protected $table = 'empresa_configuracion';
    protected $primaryKey = 'id_empresa';
    public $timestamps = false;
    protected $fillable = [
        'ruc',
        'razon_social',
        'nombre_comercial',
        'logo_url',
        'direccion_fiscal',
        'telefono_contacto',
        'correo_contacto',
        'ubigeo',
        'porcentaje_igv',
        'moneda',
        'horario_atencion',
        'mensaje_operativo',
        'estado',
    ];
}
