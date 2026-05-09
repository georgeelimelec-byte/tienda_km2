<?php

namespace Modules\Storefront\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Modules\Inventory\Models\Categoria;
use Modules\Inventory\Models\Producto;
use Modules\Storefront\Models\Promocion;
use Modules\Storefront\Services\OperationalAudit;

class AdminPromocionController extends Controller
{
    public function index()
    {
        $promociones = Promocion::with(['productos', 'categorias'])
            ->orderByDesc('id_promocion')
            ->paginate(15);
        $productos = Producto::where('estado', 'Activo')->orderBy('nombre_base')->get();
        $categorias = Categoria::where('estado', 'Activo')->orderBy('nombre')->get();

        return view('storefront::admin.promociones', compact('promociones', 'productos', 'categorias'));
    }

    public function store(Request $request, OperationalAudit $audit)
    {
        $data = $this->validatedData($request);

        DB::transaction(function () use ($data, $audit, $request) {
            $promocion = Promocion::create($this->promotionPayload($data));
            $this->syncTargets($promocion, $data);

            $audit->log(
                'crear_promocion',
                'promociones',
                $promocion->id_promocion,
                "Promocion {$promocion->nombre} creada",
                null,
                $this->auditPayload($promocion),
                $request
            );
        });

        return back()->with('success', 'Promocion creada correctamente.');
    }

    public function update(Request $request, int $id, OperationalAudit $audit)
    {
        $data = $this->validatedData($request);

        DB::transaction(function () use ($id, $data, $audit, $request) {
            $promocion = Promocion::with(['productos', 'categorias'])->findOrFail($id);
            $old = $this->auditPayload($promocion);

            $promocion->update($this->promotionPayload($data));
            $this->syncTargets($promocion, $data);
            $promocion->load(['productos', 'categorias']);

            $audit->log(
                'actualizar_promocion',
                'promociones',
                $promocion->id_promocion,
                "Promocion {$promocion->nombre} actualizada",
                $old,
                $this->auditPayload($promocion),
                $request
            );
        });

        return back()->with('success', 'Promocion actualizada correctamente.');
    }

    public function destroy(Request $request, int $id, OperationalAudit $audit)
    {
        DB::transaction(function () use ($id, $audit, $request) {
            $promocion = Promocion::with(['productos', 'categorias'])->findOrFail($id);
            $old = $this->auditPayload($promocion);

            $audit->log(
                'eliminar_promocion',
                'promociones',
                $promocion->id_promocion,
                "Promocion {$promocion->nombre} eliminada",
                $old,
                null,
                $request
            );

            $promocion->delete();
        });

        return back()->with('success', 'Promocion eliminada correctamente.');
    }

    private function validatedData(Request $request): array
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:120',
            'descripcion' => 'nullable|string',
            'tipo_descuento' => 'required|in:Porcentaje,Monto',
            'valor_descuento' => 'required|numeric|min:0.01',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'estado' => 'required|in:Activo,Inactivo',
            'producto_ids' => 'nullable|array',
            'producto_ids.*' => 'integer|exists:productos,id_producto',
            'categoria_ids' => 'nullable|array',
            'categoria_ids.*' => 'integer|exists:categorias,id_categoria',
        ]);

        if (empty($data['producto_ids']) && empty($data['categoria_ids'])) {
            throw ValidationException::withMessages([
                'promocion' => 'Selecciona al menos un producto o una categoria para aplicar la promocion.',
            ]);
        }

        return $data;
    }

    private function promotionPayload(array $data): array
    {
        return [
            'nombre' => $data['nombre'],
            'descripcion' => $data['descripcion'] ?? null,
            'tipo_descuento' => $data['tipo_descuento'],
            'valor_descuento' => $data['valor_descuento'],
            'fecha_inicio' => $data['fecha_inicio'] ?? null,
            'fecha_fin' => $data['fecha_fin'] ?? null,
            'estado' => $data['estado'],
        ];
    }

    private function syncTargets(Promocion $promocion, array $data): void
    {
        $promocion->productos()->sync($data['producto_ids'] ?? []);
        $promocion->categorias()->sync($data['categoria_ids'] ?? []);
    }

    private function auditPayload(Promocion $promocion): array
    {
        return [
            'nombre' => $promocion->nombre,
            'tipo_descuento' => $promocion->tipo_descuento,
            'valor_descuento' => $promocion->valor_descuento,
            'estado' => $promocion->estado,
            'productos' => $promocion->productos->pluck('nombre_base')->values()->all(),
            'categorias' => $promocion->categorias->pluck('nombre')->values()->all(),
        ];
    }
}
