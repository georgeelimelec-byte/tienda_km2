<?php

namespace Modules\Storefront\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Modules\Inventory\Models\Categoria;
use Modules\Inventory\Models\Producto;

class Promocion extends Model
{
    protected $table = 'promociones';
    protected $primaryKey = 'id_promocion';

    protected $fillable = [
        'nombre',
        'descripcion',
        'tipo_descuento',
        'valor_descuento',
        'fecha_inicio',
        'fecha_fin',
        'estado',
    ];

    protected $casts = [
        'valor_descuento' => 'decimal:2',
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
    ];

    public function productos()
    {
        return $this->belongsToMany(
            Producto::class,
            'promocion_productos',
            'id_promocion',
            'id_producto'
        );
    }

    public function categorias()
    {
        return $this->belongsToMany(
            Categoria::class,
            'promocion_categorias',
            'id_promocion',
            'id_categoria'
        );
    }

    public function scopeActivas(Builder $query): Builder
    {
        $today = now()->toDateString();

        return $query->where('estado', 'Activo')
            ->where(function ($dateQuery) use ($today) {
                $dateQuery->whereNull('fecha_inicio')->orWhereDate('fecha_inicio', '<=', $today);
            })
            ->where(function ($dateQuery) use ($today) {
                $dateQuery->whereNull('fecha_fin')->orWhereDate('fecha_fin', '>=', $today);
            });
    }
}
