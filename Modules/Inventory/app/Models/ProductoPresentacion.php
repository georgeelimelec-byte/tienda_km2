<?php

namespace Modules\Inventory\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo Eloquent para la tabla 'productos_presentaciones'.
 * Entidad central del inventario: cada variante tiene su propio stock, precio y barcode.
 *
 * @property int $id_presentacion
 * @property int $id_producto
 * @property int $id_unidad
 * @property string $nombre_variante
 * @property string|null $codigo_barras
 * @property float $costo_compra
 * @property float $precio
 * @property float|null $precio_oferta
 * @property int $stock
 * @property int $stock_minimo
 * @property string $estado
 */
class ProductoPresentacion extends Model
{
    protected $table = 'productos_presentaciones';
    protected $primaryKey = 'id_presentacion';
    public $timestamps = false;

    protected $fillable = [
        'id_producto', 'id_unidad', 'nombre_variante', 'codigo_barras',
        'costo_compra', 'precio', 'precio_oferta', 'stock', 'stock_minimo', 'estado',
    ];

    protected $casts = [
        'precio' => 'decimal:2',
        'precio_oferta' => 'decimal:2',
        'costo_compra' => 'decimal:2',
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto', 'id_producto');
    }

    public function unidadMedida()
    {
        return $this->belongsTo(UnidadMedida::class, 'id_unidad', 'id_unidad');
    }

    public function imagenes()
    {
        return $this->hasMany(ProductoImagen::class, 'id_presentacion', 'id_presentacion')
            ->orderBy('orden');
    }

    /**
     * Verifica si el stock está por debajo del mínimo.
     */
    public function isLowStock(): bool
    {
        return $this->stock <= $this->stock_minimo;
    }

    /**
     * Retorna el precio efectivo (oferta si existe, sino el normal).
     */
    public function getPrecioEfectivoAttribute(): float
    {
        return (float) ($this->precio_oferta ?? $this->precio);
    }

    public function getTieneOfertaAttribute(): bool
    {
        return $this->precio_oferta !== null && (float) $this->precio_oferta < (float) $this->precio;
    }

    public function getImagenPrincipalUrlAttribute(): string
    {
        $url = null;

        if ($this->relationLoaded('imagenes')) {
            $url = optional($this->imagenes->first())->imagen_url;
        }

        if (!$url && $this->relationLoaded('producto') && $this->producto) {
            return $this->producto->imagen_principal_url;
        }

        return Producto::normalizeImageUrl($url);
    }

}
