<?php

namespace Modules\Storefront\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Inventory\Models\Categoria;

class AdminCategoriaController extends Controller
{
    public function index()
    {
        $categoriasTree = Categoria::whereNull('id_categoria_padre')
            ->with(['hijos' => function ($q) {
                $q->with(['hijos' => fn ($subQuery) => $subQuery->orderBy('nombre')])
                    ->orderBy('nombre');
            }])
            ->withCount('productos')
            ->orderBy('nombre')
            ->get();

        $categorias = Categoria::with('padre')
            ->withCount('productos', 'hijos')
            ->orderBy('nombre')
            ->get();

        return view('storefront::admin.categorias', compact('categoriasTree', 'categorias'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:100',
            'id_categoria_padre' => 'nullable|exists:categorias_producto,id_categoria',
            'estado' => 'required|in:Activo,Inactivo',
        ]);

        Categoria::create([
            'nombre' => $data['nombre'],
            'id_categoria_padre' => $data['id_categoria_padre'] ?: null,
            'estado' => $data['estado'],
        ]);

        return back()->with('success', 'Categoria creada correctamente');
    }

    public function update(Request $request, int $id)
    {
        $categoria = Categoria::findOrFail($id);

        $data = $request->validate([
            'nombre' => 'required|string|max:100',
            'id_categoria_padre' => 'nullable|exists:categorias_producto,id_categoria',
            'estado' => 'required|in:Activo,Inactivo',
        ]);

        $parentId = $data['id_categoria_padre'] ? (int) $data['id_categoria_padre'] : null;
        if ($parentId === $categoria->id_categoria) {
            return back()->withErrors(['id_categoria_padre' => 'Una categoria no puede ser su propia categoria padre.']);
        }

        if ($parentId && $this->isDescendant($parentId, $categoria->id_categoria)) {
            return back()->withErrors(['id_categoria_padre' => 'No se puede mover una categoria dentro de una de sus subcategorias.']);
        }

        $categoria->update([
            'nombre' => $data['nombre'],
            'id_categoria_padre' => $parentId,
            'estado' => $data['estado'],
        ]);

        return back()->with('success', 'Categoria actualizada correctamente');
    }

    public function destroy(int $id)
    {
        $categoria = Categoria::findOrFail($id);

        if ($categoria->hijos()->exists()) {
            return back()->withErrors(['categoria' => 'No se puede eliminar una categoria que tiene subcategorias.']);
        }

        if ($categoria->productos()->exists()) {
            return back()->withErrors(['categoria' => 'No se puede eliminar una categoria con productos asignados.']);
        }

        $categoria->delete();

        return back()->with('success', 'Categoria eliminada correctamente');
    }

    private function isDescendant(int $candidateId, int $categoryId): bool
    {
        $current = Categoria::find($candidateId);

        while ($current) {
            if ((int) $current->id_categoria_padre === $categoryId) {
                return true;
            }

            $current = $current->id_categoria_padre
                ? Categoria::find($current->id_categoria_padre)
                : null;
        }

        return false;
    }
}
