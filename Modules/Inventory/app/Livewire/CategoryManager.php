<?php

namespace Modules\Inventory\Livewire;

use Livewire\Component;
use Modules\Inventory\Models\Categoria;
use Illuminate\Support\Facades\DB;

class CategoryManager extends Component
{
    public $isModalOpen = false;

    public $categoryId = null;
    public $nombre = '';
    public $id_categoria_padre = '';

    public function create($padreId = null)
    {
        $this->resetInput();
        if ($padreId) {
            $this->id_categoria_padre = $padreId;
        }
        $this->isModalOpen = true;
    }

    public function resetInput()
    {
        $this->categoryId = null;
        $this->nombre = '';
        $this->id_categoria_padre = '';
        $this->resetValidation();
        $this->resetErrorBag();
    }

    public function edit($id)
    {
        $this->resetInput();
        $categoria = Categoria::findOrFail($id);
        
        $this->categoryId = $categoria->id_categoria;
        $this->nombre = $categoria->nombre;
        $this->id_categoria_padre = $categoria->id_categoria_padre;
        
        $this->isModalOpen = true;
    }

    public function save()
    {
        $this->validate([
            'nombre' => 'required|string|max:255',
            'id_categoria_padre' => 'nullable|exists:categorias_producto,id_categoria',
        ]);

        // Evitar que una categoría sea padre de sí misma
        if ($this->categoryId && $this->categoryId == $this->id_categoria_padre) {
            $this->addError('id_categoria_padre', 'Una categoría no puede ser subcategoría de sí misma.');
            return;
        }

        try {
            if ($this->categoryId) {
                Categoria::findOrFail($this->categoryId)->update([
                    'nombre' => $this->nombre,
                    'id_categoria_padre' => $this->id_categoria_padre ?: null,
                ]);
            } else {
                Categoria::create([
                    'nombre' => $this->nombre,
                    'id_categoria_padre' => $this->id_categoria_padre ?: null,
                    'estado' => 'Activo'
                ]);
            }

            $this->isModalOpen = false;
            $this->resetInput();
            session()->flash('message', 'Categoría guardada exitosamente.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al guardar: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        DB::beginTransaction();
        try {
            $categoria = Categoria::findOrFail($id);
            if ($categoria->hijos()->count() > 0) {
                session()->flash('error', 'No se puede eliminar la categoría porque contiene subcategorías.');
                DB::rollBack();
                return;
            }
            if ($categoria->productos()->count() > 0) {
                session()->flash('error', 'No se puede eliminar porque hay productos asignados a esta categoría.');
                DB::rollBack();
                return;
            }
            
            $categoria->delete();
            DB::commit();
            session()->flash('message', 'Categoría eliminada exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Ocurrió un error al eliminar: ' . $e->getMessage());
        }
    }

    public function render()
    {
        // Traemos el árbol jerárquico cargando hasta 3 niveles por seguridad en la vista (Padre -> Hijo -> Nieto)
        $categoriasTree = Categoria::whereNull('id_categoria_padre')
            ->where('estado', 'Activo')
            ->with(['hijos' => function($q) {
                $q->with('hijos');
            }])
            ->orderBy('nombre')
            ->get();

        // Lista plana para el select de "Categoría Padre"
        $todasCategorias = Categoria::where('estado', 'Activo')
            ->orderBy('nombre')
            ->get();

        return view('inventory::livewire.category-manager', [
            'categoriasTree' => $categoriasTree,
            'todasCategorias' => $todasCategorias
        ]);
    }
}
