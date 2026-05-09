<?php

namespace Modules\Storefront\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Storefront\Models\AuditoriaOperativa;

class OperationalAudit
{
    public function log(
        string $accion,
        string $entidad,
        ?int $entidadId,
        string $descripcion,
        mixed $valorAnterior = null,
        mixed $valorNuevo = null,
        ?Request $request = null
    ): void {
        $user = Auth::user();

        AuditoriaOperativa::create([
            'id_usuario' => $user->id_usuario ?? null,
            'rol' => $user->role->nombre_rol ?? null,
            'accion' => $accion,
            'entidad' => $entidad,
            'entidad_id' => $entidadId,
            'descripcion' => $descripcion,
            'valor_anterior' => $this->stringValue($valorAnterior),
            'valor_nuevo' => $this->stringValue($valorNuevo),
            'ip' => $request?->ip(),
            'dispositivo' => $request?->userAgent(),
        ]);
    }

    private function stringValue(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_scalar($value)) {
            return (string) $value;
        }

        return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
