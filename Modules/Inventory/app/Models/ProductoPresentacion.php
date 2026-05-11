<?php

namespace Modules\Inventory\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo Eloquent para la tabla 'presentaciones_producto'.
 * Entidad central del inventario web: cada variante tiene su propio stock web, precio y barcode.
 *
 * @property int $id_presentacion
 * @property int $id_producto
 * @property int $id_unidad
 * @property string $nombre_variante
 * @property string|null $codigo_barras
 * @property float $costo_reposicion
 * @property float $precio
 * @property float|null $precio_referencial
 * @property int $stock_web
 * @property int $stock_web_minimo
 * @property string $estado
 */
class ProductoPresentacion extends Model
{
    protected $table = 'presentaciones_producto';
    protected $primaryKey = 'id_presentacion';
    public $timestamps = false;

    protected $fillable = [
        'id_producto', 'id_unidad', 'nombre_variante', 'codigo_barras',
        'costo_reposicion', 'precio', 'precio_referencial', 'stock_web', 'stock_web_minimo', 'estado',
    ];

    protected $casts = [
        'precio' => 'decimal:2',
        'precio_referencial' => 'decimal:2',
        'costo_reposicion' => 'decimal:2',
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
        return $this->stock_web <= $this->stock_web_minimo;
    }

    /**
     * Retorna el precio efectivo, aplicando promociones activas si corresponde.
     */
    public function getPrecioEfectivoAttribute(): float
    {
        return app(\Modules\Storefront\Services\PromotionPricingService::class)
            ->priceFor($this)['final_price'];
    }

    public function getTieneOfertaAttribute(): bool
    {
        return $this->tiene_promocion || (
            $this->precio_referencial !== null && (float) $this->precio_referencial > (float) $this->precio
        );
    }

    public function getTienePromocionAttribute(): bool
    {
        return app(\Modules\Storefront\Services\PromotionPricingService::class)
            ->priceFor($this)['has_promotion'];
    }

    public function getPromocionActivaAttribute()
    {
        return app(\Modules\Storefront\Services\PromotionPricingService::class)
            ->priceFor($this)['promotion'];
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
