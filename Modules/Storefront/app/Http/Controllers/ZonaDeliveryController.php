<?php

namespace Modules\Storefront\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Storefront\Models\ZonaDelivery;

class ZonaDeliveryController extends Controller
{
    public function index()
    {
        $zonas = ZonaDelivery::orderBy('id_zona', 'desc')->get();
        return view('storefront::admin.zonas', compact('zonas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'tarifa' => 'required|numeric|min:0',
        ]);

        ZonaDelivery::create([
            'nombre' => $request->nombre,
            'tarifa' => $request->tarifa,
            'estado' => $request->estado ?? 'Activo',
        ]);

        return redirect()->route('admin.zonas.index')->with('success', 'Zona creada exitosamente');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'tarifa' => 'required|numeric|min:0',
            'estado' => 'required|in:Activo,Inactivo',
        ]);

        $zona = ZonaDelivery::findOrFail($id);
        $zona->update([
            'nombre' => $request->nombre,
            'tarifa' => $request->tarifa,
            'estado' => $request->estado,
        ]);

        return redirect()->route('admin.zonas.index')->with('success', 'Zona actualizada exitosamente');
    }

    public function destroy($id)
    {
        $zona = ZonaDelivery::findOrFail($id);
        $zona->delete();

        return redirect()->route('admin.zonas.index')->with('success', 'Zona eliminada exitosamente');
    }
}
