<?php

namespace Modules\Inventory\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id_categoria
 * @property int|null $id_categoria_padre
 * @property string $nombre
 * @property string $estado
 */
class Categoria extends Model
{
    protected $table = 'categorias';
    protected $primaryKey = 'id_categoria';
    public $timestamps = false;

    protected $fillable = ['id_categoria_padre', 'nombre', 'estado'];

    public function padre()
    {
        return $this->belongsTo(self::class, 'id_categoria_padre', 'id_categoria');
    }

    public function hijos()
    {
        return $this->hasMany(self::class, 'id_categoria_padre', 'id_categoria');
    }

    public function productos()
    {
        return $this->hasMany(Producto::class, 'id_categoria', 'id_categoria');
    }
}
