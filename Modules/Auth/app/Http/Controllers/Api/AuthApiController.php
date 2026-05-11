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
        $data = $request->validate([
            'nombre' => 'required|max:150',
            'email' => 'required|email|unique:clientes_web,email',
            'numero_whatsapp' => 'required_without:telefono|string|max:20',
            'telefono' => 'nullable|string|max:20',
            'password' => 'required|string|min:6',
        ], [], [
            'numero_whatsapp' => 'numero de WhatsApp',
            'telefono' => 'numero de WhatsApp',
        ]);
        $numeroWhatsapp = $data['numero_whatsapp'] ?? $data['telefono'] ?? null;

        $cliente = Cliente::create([
            'nombre_o_razon_social' => $data['nombre'],
            'email' => $data['email'],
            'celular' => $numeroWhatsapp,
            'password' => Hash::make($data['password']),
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
            'numero_whatsapp' => $cliente->celular,
            'telefono' => $cliente->celular,
        ];
    }
}
