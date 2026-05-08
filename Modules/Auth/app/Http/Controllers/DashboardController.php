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
        // Redirige al nuevo dashboard "hub" que creamos para el Storefront y Kanban
        return view('storefront::admin.dashboard');
    }
}
