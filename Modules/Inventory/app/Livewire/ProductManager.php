<?php

namespace Modules\Inventory\Livewire;

use App\Support\SafeImageUpload;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Modules\Inventory\Models\Producto;
use Modules\Inventory\Models\ProductoImagen;
use Modules\Inventory\Models\ProductoPresentacion;
use Modules\Inventory\Models\Categoria;
use Modules\Inventory\Models\UnidadMedida;

class ProductManager extends Component
{
    use WithPagination;
    use WithFileUploads;

    public $search = '';
    public $isModalOpen = false;

    // Campos Producto Maestro
    public $productId = null;
    public $nombre_base = '';
    public $descripcion = '';
    public $id_categoria = '';
    
    // Array para múltiples Variantes (Presentaciones)
    public $presentaciones = []; 
    
    public function mount() {}

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updated($propertyName, $value = null)
    {
        if (str_contains($propertyName, 'imagenes_archivos')) {
            $this->validate([
                'presentaciones.*.imagenes_archivos.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            ]);

            $this->assertPresentationUploads();
        }
    }

    public function create()
    {
        $this->resetInput();
        $this->addPresentacion(); // Agregar variante inicial por defecto
        $this->isModalOpen = true;
    }

    public function resetInput()
    {
        $this->productId = null;
        $this->nombre_base = '';
        $this->descripcion = '';
        $this->id_categoria = '';
        $this->presentaciones = [];
        $this->resetValidation();
        $this->resetErrorBag();
    }

    public function addPresentacion()
    {
        $this->presentaciones[] = [
            'id_presentacion' => null,
            'nombre_variante' => 'Unidad',
            'codigo_barras' => '',
            'costo_reposicion' => 0,
            'precio' => 0,
            'stock' => 0,
            'stock_minimo' => 0,
            'id_unidad' => 1,
            'galeria_urls' => '',
            'imagenes_archivos' => [],
        ];
    }

    public function removePresentacion($index)
    {
        unset($this->presentaciones[$index]);
        $this->presentaciones = array_values($this->presentaciones); // reindex
    }

    public function removePresentationUrl(int $presentationIndex, int $imageIndex): void
    {
        if (!isset($this->presentaciones[$presentationIndex])) {
            return;
        }

        $urls = $this->imageUrls($this->presentaciones[$presentationIndex]['galeria_urls'] ?? null);
        unset($urls[$imageIndex]);

        $this->presentaciones[$presentationIndex]['galeria_urls'] = implode("\n", array_values($urls));
    }

    public function removePresentationUpload(int $presentationIndex, int $imageIndex): void
    {
        if (!isset($this->presentaciones[$presentationIndex]['imagenes_archivos'][$imageIndex])) {
            return;
        }

        unset($this->presentaciones[$presentationIndex]['imagenes_archivos'][$imageIndex]);
        $this->presentaciones[$presentationIndex]['imagenes_archivos'] = array_values(
            $this->presentaciones[$presentationIndex]['imagenes_archivos']
        );
    }

    public function presentationImageUrls(int $presentationIndex): array
    {
        return $this->imageUrls($this->presentaciones[$presentationIndex]['galeria_urls'] ?? null);
    }

    public function temporaryUploadPreview(int $presentationIndex, int $imageIndex): ?string
    {
        $file = $this->presentaciones[$presentationIndex]['imagenes_archivos'][$imageIndex] ?? null;

        if (!$file || !method_exists($file, 'temporaryUrl')) {
            return null;
        }

        try {
            return $file->temporaryUrl();
        } catch (\Throwable $e) {
            return null;
        }
    }

    public function edit($id)
    {
        $this->resetInput();
        $product = Producto::with('presentaciones.imagenes')->findOrFail($id);
        
        $this->productId = $product->id_producto;
        $this->nombre_base = $product->nombre_base;
        $this->descripcion = $product->descripcion;
        $this->id_categoria = $product->id_categoria;
        
        foreach ($product->presentaciones as $p) {
            $this->presentaciones[] = [
                'id_presentacion' => $p->id_presentacion,
                'nombre_variante' => $p->nombre_variante,
                'codigo_barras' => $p->codigo_barras,
                'costo_reposicion' => $p->costo_reposicion,
                'precio' => $p->precio,
                'stock' => $p->stock,
                'stock_minimo' => $p->stock_minimo,
                'id_unidad' => $p->id_unidad,
                'galeria_urls' => $p->imagenes->pluck('imagen_url')->implode("\n"),
                'imagenes_archivos' => [],
            ];
        }

        $this->isModalOpen = true;
    }

    public function save()
    {
        $this->validate([
            'nombre_base' => 'required|string|max:255',
            'id_categoria' => 'required',
            'presentaciones' => 'required|array|min:1',
            'presentaciones.*.nombre_variante' => 'required|string|max:255',
            'presentaciones.*.precio' => 'required|numeric|min:0',
            'presentaciones.*.costo_reposicion' => 'nullable|numeric|min:0',
            'presentaciones.*.stock' => 'required|integer|min:0',
            'presentaciones.*.stock_minimo' => 'nullable|integer|min:0',
            'presentaciones.*.galeria_urls' => 'nullable|string',
            'presentaciones.*.imagenes_archivos.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ], [
            'presentaciones.min' => 'El producto debe tener al menos una variante (presentación).',
            'presentaciones.*.nombre_variante.required' => 'El nombre de la variante es requerido.',
            'presentaciones.*.precio.required' => 'El precio de la variante es requerido.',
        ]);

        DB::beginTransaction();
        try {
            if ($this->productId) {
                $product = Producto::findOrFail($this->productId);
                $product->update([
                    'nombre_base' => $this->nombre_base,
                    'descripcion' => $this->descripcion,
                    'id_categoria' => $this->id_categoria,
                ]);
            } else {
                $product = Producto::create([
                    'nombre_base' => $this->nombre_base,
                    'descripcion' => $this->descripcion,
                    'id_categoria' => $this->id_categoria,
                    'estado' => 'Activo'
                ]);
            }

            // Guardado y Sincronización Transaccional de Presentaciones
            $existingIds = [];
            foreach ($this->presentaciones as $pData) {
                if (!empty($pData['id_presentacion'])) {
                    $pres = ProductoPresentacion::findOrFail($pData['id_presentacion']);
                    $pres->update([
                        'nombre_variante' => $pData['nombre_variante'],
                        'codigo_barras' => !empty($pData['codigo_barras']) ? $pData['codigo_barras'] : null,
                        'costo_reposicion' => $pData['costo_reposicion'] ?: 0,
                        'precio' => $pData['precio'],
                        'stock' => max(0, (int) ($pData['stock'] ?? 0)),
                        'stock_minimo' => $pData['stock_minimo'] ?: 0,
                        'id_unidad' => $pData['id_unidad'] ?: 1,
                    ]);
                    $existingIds[] = $pres->id_presentacion;
                } else {
                    $pres = ProductoPresentacion::create([
                        'id_producto' => $product->id_producto,
                        'nombre_variante' => $pData['nombre_variante'],
                        'codigo_barras' => !empty($pData['codigo_barras']) ? $pData['codigo_barras'] : null,
                        'costo_reposicion' => $pData['costo_reposicion'] ?: 0,
                        'precio' => $pData['precio'],
                        'stock' => max(0, (int) ($pData['stock'] ?? 0)),
                        'stock_minimo' => $pData['stock_minimo'] ?: 0,
                        'id_unidad' => $pData['id_unidad'] ?: 1,
                        'estado' => 'Activo'
                    ]);
                    $existingIds[] = $pres->id_presentacion;
                }

                $urls = array_merge(
                    $this->imageUrls($pData['galeria_urls'] ?? null),
                    $this->storeUploadedImages($pData['imagenes_archivos'] ?? [])
                );

                $this->syncPresentationImages($product, $pres, $urls);
            }
            
            // Eliminar variantes que el usuario haya quitado con la X
            ProductoImagen::where('id_producto', $product->id_producto)
                ->whereNotNull('id_presentacion')
                ->whereNotIn('id_presentacion', $existingIds)
                ->delete();

            ProductoPresentacion::where('id_producto', $product->id_producto)
                ->whereNotIn('id_presentacion', $existingIds)
                ->delete();

            DB::commit();
            
            $this->isModalOpen = false;
            $this->resetInput();
            session()->flash('message', 'Producto y variantes guardadas exitosamente.');
        } catch (ValidationException $e) {
            DB::rollBack();
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error crítico al procesar la información de BD: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        DB::beginTransaction();
        try {
            $product = Producto::findOrFail($id);
            // La BD debería tener cascade, pero lo forzamos manual aquí para seguridad
            ProductoImagen::where('id_producto', $id)->delete();
            ProductoPresentacion::where('id_producto', $id)->delete();
            $product->delete();
            DB::commit();
            session()->flash('message', 'Registro purgado existosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Integridad de DB impedida: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $products = Producto::with('categoria', 'presentaciones.unidadMedida', 'presentaciones.imagenes')
            ->where(function($q) {
                $q->where('nombre_base', 'like', '%' . $this->search . '%')
                  ->orWhereHas('presentaciones', function ($supQuery) {
                      $supQuery->where('codigo_barras', 'like', '%' . $this->search . '%')
                               ->orWhere('nombre_variante', 'like', '%' . $this->search . '%');
                  });
            })
            ->orderBy('id_producto', 'desc')
            ->paginate(12);
            
        $categorias = Categoria::where('estado', 'Activo')->get();
        $unidades = UnidadMedida::where('estado', 'Activo')->get();

        return view('inventory::livewire.product-manager', [
            'products' => $products,
            'categorias' => $categorias,
            'unidades' => $unidades,
        ]);
    }

    private function imageUrls(?string $galleryUrls): array
    {
        $urls = [];

        foreach (preg_split('/[\r\n,]+/', (string) $galleryUrls) as $url) {
            $url = trim($url);
            if ($url !== '') {
                $urls[] = $url;
            }
        }

        return array_values(array_unique($urls));
    }

    private function storeUploadedImages($files): array
    {
        if (!$files) {
            return [];
        }

        $files = is_array($files) ? $files : [$files];
        $urls = [];
        foreach ($files as $file) {
            if (!$file || !method_exists($file, 'getClientOriginalExtension')) {
                continue;
            }

            $urls[] = SafeImageUpload::store($file, 'images/catalogo', 'presentacion');
        }

        return $urls;
    }

    private function assertPresentationUploads(): void
    {
        foreach ($this->presentaciones as $presentation) {
            $files = $presentation['imagenes_archivos'] ?? [];
            $files = is_array($files) ? $files : [$files];

            foreach ($files as $file) {
                if ($file && method_exists($file, 'getRealPath')) {
                    SafeImageUpload::assertValid($file);
                }
            }
        }
    }

    private function syncPresentationImages(Producto $product, ProductoPresentacion $presentation, array $urls): void
    {
        $urls = array_values(array_unique(array_filter($urls)));

        ProductoImagen::where('id_presentacion', $presentation->id_presentacion)->delete();

        foreach ($urls as $index => $url) {
            ProductoImagen::create([
                'id_producto' => $product->id_producto,
                'id_presentacion' => $presentation->id_presentacion,
                'imagen_url' => $url,
                'orden' => $index,
            ]);
        }
    }

}
