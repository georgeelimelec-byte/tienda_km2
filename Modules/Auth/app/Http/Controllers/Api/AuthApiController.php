<?php

namespace Modules\Auth\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Modules\Storefront\Models\Cliente;

class AuthApiController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $cliente = Cliente::where('email', $request->email)->first();

        if (!$cliente || !Hash::check($request->password, (string) $cliente->password)) {
            return response()->json([
                'message' => 'Credenciales incorrectas.',
            ], 401);
        }

        return response()->json([
            'token' => $cliente->createToken('storefront')->plainTextToken,
            'cliente' => $this->clientePayload($cliente),
        ]);
    }

    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'nombre' => 'required|max:150',
            'email' => 'required|email|unique:clientes,email',
            'telefono' => 'nullable|max:20',
            'password' => 'required|string|min:6',
        ]);

        $cliente = Cliente::create([
            'nombre_o_razon_social' => $request->nombre,
            'email' => $request->email,
            'celular' => $request->telefono,
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'message' => 'Registro exitoso.',
            'cliente' => $this->clientePayload($cliente),
        ], 201);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json($this->clientePayload($request->user()));
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Sesion cerrada.',
        ]);
    }

    private function clientePayload(Cliente $cliente): array
    {
        return [
            'id' => $cliente->id_cliente,
            'nombre' => $cliente->nombre_o_razon_social,
            'email' => $cliente->email,
            'telefono' => $cliente->celular,
        ];
    }
}
