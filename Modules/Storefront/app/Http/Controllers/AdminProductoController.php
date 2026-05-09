<?php

namespace Modules\Storefront\Http\Controllers;

use App\Support\SafeImageUpload;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Inventory\Models\Categoria;
use Modules\Inventory\Models\Producto;
use Modules\Inventory\Models\ProductoImagen;
use Modules\Inventory\Models\ProductoPresentacion;
use Modules\Storefront\Services\OperationalAudit;
use Modules\Storefront\Services\StockWebService;

class AdminProductoController extends Controller
{
    public function index()
    {
        $productos = Producto::with(['categoria', 'presentaciones.imagenes', 'imagenes'])
            ->orderBy('id_producto', 'desc')
            ->paginate(10);
        $categorias = Categoria::with('padre')->where('estado', 'Activo')->orderBy('nombre')->get();

        return view('storefront::admin.productos', compact('productos', 'categorias'));
    }

    public function store(Request $request, StockWebService $stockWeb, OperationalAudit $audit)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:150',
            'descripcion' => 'nullable|string',
            'id_categoria' => 'required|exists:categorias,id_categoria',
            'precio_venta' => 'required|numeric|min:0',
            'precio_referencial' => 'nullable|numeric|min:0|gt:precio_venta',
            'stock_web' => 'required|integer|min:0',
            'foto_archivo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'galeria_urls' => 'nullable|string',
            'nombre_variante' => 'nullable|string|max:100',
            'codigo_barras' => 'nullable|string|max:100',
            'estado' => 'nullable|in:Activo,Inactivo',
        ]);

        $imageUrl = $this->resolveImageUrl($request);
        $imageUrls = $this->imageUrls($imageUrl, $data['galeria_urls'] ?? null);

        DB::transaction(function () use ($data, $imageUrl, $imageUrls, $stockWeb, $audit, $request) {
            $producto = Producto::create([
                'nombre_base' => $data['nombre'],
                'descripcion' => $data['descripcion'] ?? null,
                'id_categoria' => $data['id_categoria'],
                'imagen_url' => $imageUrls[0] ?? $imageUrl,
                'estado' => $data['estado'] ?? 'Activo',
            ]);

            $presentacion = ProductoPresentacion::create([
                'id_producto' => $producto->id_producto,
                'id_unidad' => 1,
                'nombre_variante' => ($data['nombre_variante'] ?? '') ?: 'Unidad',
                'codigo_barras' => $data['codigo_barras'] ?? null,
                'precio' => $data['precio_venta'],
                'precio_referencial' => $data['precio_referencial'] ?? null,
                'stock_web' => 0,
                'stock_web_minimo' => 1,
                'estado' => $data['estado'] ?? 'Activo',
            ]);

            $stockWeb->adjustManual($presentacion, (int) $data['stock_web'], 'Carga inicial de stock web desde productos');
            $this->syncImages($producto, $imageUrls);
            $audit->log(
                'crear_producto',
                'productos',
                $producto->id_producto,
                "Producto {$producto->nombre_base} creado para la tienda virtual",
                null,
                [
                    'precio' => $presentacion->precio,
                    'precio_referencial' => $presentacion->precio_referencial,
                    'stock_web' => $data['stock_web'],
                ],
                $request
            );
        });

        return back()->with('success', 'Producto creado exitosamente');
    }

    public function update(Request $request, $id, StockWebService $stockWeb, OperationalAudit $audit)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:150',
            'descripcion' => 'nullable|string',
            'id_categoria' => 'required|exists:categorias,id_categoria',
            'precio_venta' => 'required|numeric|min:0',
            'precio_referencial' => 'nullable|numeric|min:0|gt:precio_venta',
            'stock_web' => 'required|integer|min:0',
            'estado' => 'required|in:Activo,Inactivo',
            'foto_archivo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'galeria_urls' => 'nullable|string',
            'nombre_variante' => 'nullable|string|max:100',
            'codigo_barras' => 'nullable|string|max:100',
        ]);

        $imageUrl = $this->resolveImageUrl($request);
        $imageUrls = $this->imageUrls($imageUrl, $data['galeria_urls'] ?? null);

        DB::transaction(function () use ($data, $id, $imageUrl, $imageUrls, $stockWeb, $audit, $request) {
            $producto = Producto::with(['presentaciones.imagenes', 'imagenes'])->findOrFail($id);
            $oldProduct = $producto->only(['nombre_base', 'descripcion', 'id_categoria', 'estado']);
            $producto->update([
                'nombre_base' => $data['nombre'],
                'descripcion' => $data['descripcion'] ?? null,
                'id_categoria' => $data['id_categoria'],
                'imagen_url' => $imageUrls[0] ?? $imageUrl,
                'estado' => $data['estado'],
            ]);

            $presentacion = $producto->presentaciones->first();
            if ($presentacion) {
                $oldPresentation = $presentacion->only(['nombre_variante', 'codigo_barras', 'precio', 'precio_referencial', 'stock_web', 'estado']);
                $presentacion->update([
                    'nombre_variante' => ($data['nombre_variante'] ?? '') ?: 'Unidad',
                    'codigo_barras' => $data['codigo_barras'] ?? null,
                    'precio' => $data['precio_venta'],
                    'precio_referencial' => $data['precio_referencial'] ?? null,
                    'estado' => $data['estado'],
                ]);

                if ((int) $oldPresentation['stock_web'] !== (int) $data['stock_web']) {
                    $stockWeb->adjustManual($presentacion, (int) $data['stock_web'], 'Ajuste manual de stock web desde productos');
                }
            } else {
                $oldPresentation = null;
                $presentacion = ProductoPresentacion::create([
                    'id_producto' => $producto->id_producto,
                    'id_unidad' => 1,
                    'nombre_variante' => ($data['nombre_variante'] ?? '') ?: 'Unidad',
                    'codigo_barras' => $data['codigo_barras'] ?? null,
                    'precio' => $data['precio_venta'],
                    'precio_referencial' => $data['precio_referencial'] ?? null,
                    'stock_web' => 0,
                    'stock_web_minimo' => 1,
                    'estado' => $data['estado'],
                ]);
                $stockWeb->adjustManual($presentacion, (int) $data['stock_web'], 'Carga inicial de stock web desde productos');
            }

            $this->syncImages($producto, $imageUrls);
            $audit->log(
                'actualizar_producto',
                'productos',
                $producto->id_producto,
                "Producto {$producto->nombre_base} actualizado para la tienda virtual",
                [
                    'producto' => $oldProduct,
                    'presentacion' => $oldPresentation,
                ],
                [
                    'producto' => $producto->fresh()->only(['nombre_base', 'descripcion', 'id_categoria', 'estado']),
                    'presentacion' => $presentacion->fresh()->only(['nombre_variante', 'codigo_barras', 'precio', 'precio_referencial', 'stock_web', 'estado']),
                ],
                $request
            );
        });

        return back()->with('success', 'Producto actualizado exitosamente');
    }

    public function destroy($id, OperationalAudit $audit, Request $request)
    {
        try {
            DB::transaction(function () use ($id, $audit, $request) {
                $producto = Producto::findOrFail($id);
                $audit->log(
                    'eliminar_producto',
                    'productos',
                    $producto->id_producto,
                    "Producto {$producto->nombre_base} eliminado de la tienda virtual",
                    $producto->only(['nombre_base', 'id_categoria', 'estado']),
                    null,
                    $request
                );
                ProductoPresentacion::where('id_producto', $producto->id_producto)->delete();
                $producto->delete();
            });
        } catch (\Throwable $e) {
            return back()->withErrors(['producto' => 'No se pudo eliminar el producto porque tiene pedidos WhatsApp asociados.']);
        }

        return back()->with('success', 'Producto eliminado exitosamente');
    }

    private function resolveImageUrl(Request $request): ?string
    {
        if ($request->hasFile('foto_archivo')) {
            return SafeImageUpload::store($request->file('foto_archivo'), 'images/catalogo', 'producto');
        }

        return null;
    }

    private function imageUrls(?string $primaryUrl, ?string $galleryUrls): array
    {
        $urls = [];

        if ($primaryUrl) {
            $urls[] = $primaryUrl;
        }

        foreach (preg_split('/[\r\n,]+/', (string) $galleryUrls) as $url) {
            $url = trim($url);
            if ($url !== '') {
                $urls[] = $url;
            }
        }

        return array_values(array_unique($urls));
    }

    private function syncImages(Producto $producto, array $urls): void
    {
        ProductoImagen::where('id_producto', $producto->id_producto)
            ->whereNull('id_presentacion')
            ->delete();

        foreach ($urls as $index => $url) {
            ProductoImagen::create([
                'id_producto' => $producto->id_producto,
                'id_presentacion' => null,
                'imagen_url' => $url,
                'orden' => $index,
            ]);
        }
    }
}
