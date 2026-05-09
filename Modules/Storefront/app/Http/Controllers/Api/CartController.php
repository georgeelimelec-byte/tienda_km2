<?php

namespace Modules\Storefront\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Inventory\Models\ProductoPresentacion;
use Modules\Storefront\Models\CarritoWeb;

class CartController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $clienteId = $this->clienteId($request);
        if (!$clienteId) {
            return response()->json(['message' => 'Carrito API disponible solo para clientes autenticados.'], 403);
        }

        $items = CarritoWeb::with('presentacion.imagenes', 'presentacion.producto.imagenes')
            ->where('id_cliente', $clienteId)
            ->get();

        return response()->json($items);
    }

    public function addItem(Request $request): JsonResponse
    {
        $clienteId = $this->clienteId($request);
        if (!$clienteId) {
            return response()->json(['message' => 'Carrito API disponible solo para clientes autenticados.'], 403);
        }

        $data = $request->validate([
            'id_presentacion' => 'required|exists:productos_presentaciones,id_presentacion',
            'cantidad' => 'required|integer|min:1',
        ]);

        $presentacion = ProductoPresentacion::where('estado', 'Activo')->findOrFail($data['id_presentacion']);
        if ($data['cantidad'] > $presentacion->stock_web) {
            return response()->json(['message' => 'Stock insuficiente.'], 422);
        }

        $item = CarritoWeb::updateOrCreate(
            ['id_cliente' => $clienteId, 'id_presentacion' => $presentacion->id_presentacion],
            ['cantidad' => $data['cantidad']]
        );

        return response()->json($item->load('presentacion.imagenes', 'presentacion.producto.imagenes'), 201);
    }

    public function updateItem(Request $request, int $id): JsonResponse
    {
        $clienteId = $this->clienteId($request);
        if (!$clienteId) {
            return response()->json(['message' => 'Carrito API disponible solo para clientes autenticados.'], 403);
        }

        $data = $request->validate(['cantidad' => 'required|integer|min:1']);
        $item = CarritoWeb::where('id_cliente', $clienteId)->findOrFail($id);
        $presentacion = ProductoPresentacion::where('estado', 'Activo')->findOrFail($item->id_presentacion);
        if ($data['cantidad'] > $presentacion->stock_web) {
            return response()->json(['message' => 'Stock insuficiente.'], 422);
        }

        $item->update(['cantidad' => $data['cantidad']]);

        return response()->json($item->fresh('presentacion.imagenes', 'presentacion.producto.imagenes'));
    }

    public function removeItem(Request $request, int $id): JsonResponse
    {
        $clienteId = $this->clienteId($request);
        if (!$clienteId) {
            return response()->json(['message' => 'Carrito API disponible solo para clientes autenticados.'], 403);
        }

        CarritoWeb::where('id_cliente', $clienteId)->findOrFail($id)->delete();

        return response()->json(['message' => 'Item eliminado.']);
    }

    private function clienteId(Request $request): ?int
    {
        $user = $request->user();

        return $user && isset($user->id_cliente) ? (int) $user->id_cliente : null;
    }
}
