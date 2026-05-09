<?php

namespace Modules\Auth\Http\Controllers;

use Illuminate\Routing\Controller;

/**
 * Controlador del Dashboard principal.
 * Recopila métricas del día y las pasa a la vista.
 */
class DashboardController extends Controller
{
    public function index()
    {
        // Redirige al dashboard hub de tienda virtual y pedidos WhatsApp.
        return view('storefront::admin.dashboard');
    }
}
