<?php

namespace Modules\Inventory\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo Eloquent para la tabla 'productos'.
 *
 * @property int $id_producto
 * @property int $id_categoria
 * @property string $nombre_base
 * @property string|null $descripcion
 * @property string|null $imagen_url
 * @property string $estado
 */
class Producto extends Model
{
    protected $table = 'productos';
    protected $primaryKey = 'id_producto';
    public $timestamps = false;

    protected $fillable = [
        'id_categoria', 'nombre_base', 'descripcion', 'imagen_url', 'estado',
    ];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'id_categoria', 'id_categoria');
    }

    public function presentaciones()
    {
        return $this->hasMany(ProductoPresentacion::class, 'id_producto', 'id_producto');
    }

    public function presentacionPrincipal()
    {
        return $this->hasOne(ProductoPresentacion::class, 'id_producto', 'id_producto')
            ->where('estado', 'Activo')
            ->orderByRaw('CASE WHEN stock > 0 THEN 0 ELSE 1 END')
            ->orderBy('precio')
            ->orderBy('id_presentacion');
    }

    public function imagenes()
    {
        return $this->hasMany(ProductoImagen::class, 'id_producto', 'id_producto')
            ->whereNull('id_presentacion')
            ->orderBy('orden');
    }

    public function todasImagenes()
    {
        return $this->hasMany(ProductoImagen::class, 'id_producto', 'id_producto')
            ->orderBy('id_presentacion')
            ->orderBy('orden');
    }

    public function getNombreAttribute(): string
    {
        return $this->nombre_base;
    }

    public function getImagenPrincipalUrlAttribute(): string
    {
        $url = null;

        if ($this->relationLoaded('imagenes')) {
            $url = optional($this->imagenes->first())->imagen_url;
        }

        if (!$url && $this->relationLoaded('presentaciones')) {
            foreach ($this->presentaciones as $presentacion) {
                if ($presentacion->relationLoaded('imagenes')) {
                    $url = optional($presentacion->imagenes->first())->imagen_url;
                    if ($url) {
                        break;
                    }
                }
            }
        }

        return self::normalizeImageUrl($url ?: $this->imagen_url);
    }

    public function getStockTotalAttribute(): int
    {
        if ($this->relationLoaded('presentaciones')) {
            return (int) $this->presentaciones
                ->where('estado', 'Activo')
                ->sum('stock');
        }

        return (int) $this->presentaciones()
            ->where('estado', 'Activo')
            ->sum('stock');
    }

    public function getPrecioDesdeAttribute(): ?float
    {
        if ($this->relationLoaded('presentaciones')) {
            $precio = $this->presentaciones
                ->where('estado', 'Activo')
                ->map(fn ($presentacion) => (float) $presentacion->precio_efectivo)
                ->filter(fn ($precio) => $precio >= 0)
                ->min();

            return $precio !== null ? (float) $precio : null;
        }

        $presentacion = $this->presentaciones()
            ->where('estado', 'Activo')
            ->orderBy('precio')
            ->first();

        return $presentacion ? (float) $presentacion->precio_efectivo : null;
    }

    public static function normalizeImageUrl(?string $url): string
    {
        if (!$url) {
            return 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?auto=format&fit=crop&q=80&w=800';
        }

        if (preg_match('/^https?:\/\//i', $url)) {
            return $url;
        }

        if (str_starts_with($url, 'storage/') || str_starts_with($url, 'images/')) {
            return asset($url);
        }

        return asset('storage/' . ltrim($url, '/'));
    }
}
