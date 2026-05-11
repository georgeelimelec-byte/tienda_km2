<?php

namespace Modules\Storefront\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Cliente extends Authenticatable
{
    use HasApiTokens;

    protected $table = 'clientes_web';
    protected $primaryKey = 'id_cliente';
    public $timestamps = false;
    protected $fillable = [
        'tipo_documento', 'numero_documento', 'nombre_o_razon_social',
        'direccion', 'email', 'celular', 'password',
    ];
    protected $hidden = ['password'];

    public function carrito() { return $this->hasMany(CarritoWeb::class, 'id_cliente', 'id_cliente'); }
    public function pedidosWhatsapp() { return $this->hasMany(PedidoWhatsapp::class, 'cliente_whatsapp', 'celular'); }

    public function getTelefonoAttribute(): ?string
    {
        return $this->celular;
    }

    public function setTelefonoAttribute(?string $value): void
    {
        $this->attributes['celular'] = $value;
    }

    public function getNumeroWhatsappAttribute(): ?string
    {
        return $this->celular;
    }

    public function setNumeroWhatsappAttribute(?string $value): void
    {
        $this->attributes['celular'] = $value;
    }
}
