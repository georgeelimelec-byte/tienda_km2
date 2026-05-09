<?php

namespace Modules\Storefront\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Storefront\Models\AuditoriaOperativa;
use Modules\Storefront\Models\StockWebMovimiento;

class AdminAuditoriaController extends Controller
{
    public function index(Request $request)
    {
        $auditorias = AuditoriaOperativa::with('usuario')
            ->when($request->query('accion'), fn ($q, $accion) => $q->where('accion', $accion))
            ->when($request->query('entidad'), fn ($q, $entidad) => $q->where('entidad', $entidad))
            ->latest('created_at')
            ->paginate(25)
            ->withQueryString();

        $movimientosStock = StockWebMovimiento::with(['presentacion.producto', 'pedido', 'usuario'])
            ->latest('created_at')
            ->take(12)
            ->get();

        return view('storefront::admin.auditoria', compact('auditorias', 'movimientosStock'));
    }
}
