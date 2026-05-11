<?php

namespace Modules\Auth\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Modules\Auth\Models\Usuario;
use Modules\Storefront\Models\Cliente;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }

        return view('auth::login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:4',
        ], [
            'email.required' => 'El correo es obligatorio.',
            'email.email' => 'Ingresa un correo valido.',
            'password.required' => 'La contrasena es obligatoria.',
        ]);

        $usuario = Usuario::where('email', $request->email)->first();

        if ($usuario && Hash::check($request->password, $usuario->password_hash)) {
            if ($usuario->estado !== 'Activo') {
                return back()
                    ->withInput($request->only('email'))
                    ->withErrors(['auth' => 'Tu cuenta esta desactivada. Contacta al administrador.']);
            }

            $request->session()->forget('cliente_id');
            Auth::login($usuario);
            $request->session()->regenerate();

            return redirect()->intended(route('admin.dashboard'));
        }

        $cliente = Cliente::where('email', $request->email)->first();

        if ($cliente && $cliente->password && Hash::check($request->password, (string) $cliente->password)) {
            Auth::logout();
            $request->session()->regenerate();
            $request->session()->put('cliente_id', $cliente->id_cliente);

            return redirect()->to($request->session()->pull('cliente.intended', route('storefront.index')));
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['auth' => 'Credenciales incorrectas. Verifica tu email y contrasena.']);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->forget('cliente_id');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('auth.login')
            ->with('success', 'Sesion cerrada correctamente.');
    }
}
