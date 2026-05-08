<?php

namespace Modules\Auth\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Modules\Auth\Models\Usuario;

/**
 * Controlador de autenticación web (panel administrativo).
 * Solo recibe la petición y delega al guard de Laravel.
 */
class LoginController extends Controller
{
    /**
     * Muestra el formulario de login.
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }

        return view('auth::login');
    }

    /**
     * Procesa el login por email/password.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|min:4',
        ], [
            'email.required'    => 'El correo es obligatorio.',
            'email.email'       => 'Ingresa un correo válido.',
            'password.required' => 'La contraseña es obligatoria.',
        ]);

        // Buscar usuario por email
        $usuario = Usuario::where('email', $request->email)->first();

        if (!$usuario || !Hash::check($request->password, $usuario->password_hash)) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['auth' => 'Credenciales incorrectas. Verifica tu email y contraseña.']);
        }

        if ($usuario->estado !== 'Activo') {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['auth' => 'Tu cuenta está desactivada. Contacta al administrador.']);
        }

        Auth::login($usuario);
        $request->session()->regenerate();

        return redirect()->intended(route('admin.dashboard'));
    }

    /**
     * Cierra la sesión.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('auth.login')
            ->with('success', 'Sesión cerrada correctamente.');
    }
}
