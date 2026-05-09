@extends('layouts.admin')

@section('title', 'Auditoria')
@section('page-title', 'Auditoria operativa')
@section('page-kicker', 'Seguridad y trazabilidad')

@section('topbar-actions')
    <span class="topbar-badge"><i class="fas fa-shield-halved"></i> Acciones registradas</span>
@endsection

@section('content')
    <div class="mx-auto w-full max-w-[1500px] space-y-6">
        <section class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
            <form method="GET" action="{{ route('admin.auditoria.index') }}" class="grid gap-3 md:grid-cols-[220px_220px_auto]">
                <input type="text" name="accion" value="{{ request('accion') }}" class="rounded-lg border-gray-200 bg-gray-50 px-4 py-2.5 focus:border-brand focus:ring-brand" placeholder="Accion">
                <input type="text" name="entidad" value="{{ request('entidad') }}" class="rounded-lg border-gray-200 bg-gray-50 px-4 py-2.5 focus:border-brand focus:ring-brand" placeholder="Entidad">
                <button class="inline-flex items-center justify-center gap-2 rounded-lg bg-gray-900 px-4 py-2.5 font-bold text-white transition hover:bg-brand">
                    <i class="fas fa-filter"></i> Filtrar
                </button>
            </form>
        </section>

        <section class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-100 px-5 py-4">
                <h2 class="text-xl font-black text-gray-900">Registro de acciones</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-5 py-4 text-left text-xs font-bold uppercase text-gray-500">Fecha</th>
                            <th class="px-5 py-4 text-left text-xs font-bold uppercase text-gray-500">Usuario</th>
                            <th class="px-5 py-4 text-left text-xs font-bold uppercase text-gray-500">Accion</th>
                            <th class="px-5 py-4 text-left text-xs font-bold uppercase text-gray-500">Detalle legible</th>
                            <th class="px-5 py-4 text-left text-xs font-bold uppercase text-gray-500">IP / Dispositivo</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($auditorias as $audit)
                            <tr class="align-top">
                                <td class="px-5 py-4 text-sm text-gray-600">{{ optional($audit->created_at)->format('d/m/Y H:i:s') }}</td>
                                <td class="px-5 py-4">
                                    <div class="font-bold text-gray-900">{{ optional($audit->usuario)->nombres ?: 'Cliente web / sistema' }}</div>
                                    <div class="text-xs text-gray-500">{{ $audit->rol ?: 'Sin rol interno' }}</div>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="rounded-md bg-orange-50 px-2 py-1 text-xs font-black text-brand">{{ $audit->accion }}</span>
                                    <div class="mt-2 text-xs text-gray-500">{{ $audit->entidad }} #{{ $audit->entidad_id }}</div>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="max-w-2xl text-sm font-semibold text-gray-800">{{ $audit->descripcion }}</div>
                                    @if($audit->valor_anterior || $audit->valor_nuevo)
                                        <details class="mt-2">
                                            <summary class="cursor-pointer text-xs font-bold text-gray-500">Ver valores</summary>
                                            <div class="mt-2 grid gap-2 text-xs md:grid-cols-2">
                                                <pre class="overflow-auto rounded bg-gray-50 p-2 text-gray-600">{{ $audit->valor_anterior ?: 'Sin valor anterior' }}</pre>
                                                <pre class="overflow-auto rounded bg-gray-50 p-2 text-gray-600">{{ $audit->valor_nuevo ?: 'Sin valor nuevo' }}</pre>
                                            </div>
                                        </details>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-xs text-gray-500">
                                    <div>{{ $audit->ip ?: 'Sin IP' }}</div>
                                    <div class="mt-1 max-w-xs truncate" title="{{ $audit->dispositivo }}">{{ $audit->dispositivo ?: 'Sin dispositivo' }}</div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-16 text-center text-gray-500">No hay registros de auditoria.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="border-t border-gray-100 px-5 py-4">
                {{ $auditorias->links() }}
            </div>
        </section>

        <section class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-100 px-5 py-4">
                <h2 class="text-xl font-black text-gray-900">Ultimos movimientos de stock web</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-5 py-4 text-left text-xs font-bold uppercase text-gray-500">Fecha</th>
                            <th class="px-5 py-4 text-left text-xs font-bold uppercase text-gray-500">Producto</th>
                            <th class="px-5 py-4 text-left text-xs font-bold uppercase text-gray-500">Movimiento</th>
                            <th class="px-5 py-4 text-left text-xs font-bold uppercase text-gray-500">Stock</th>
                            <th class="px-5 py-4 text-left text-xs font-bold uppercase text-gray-500">Motivo</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($movimientosStock as $movimiento)
                            <tr>
                                <td class="px-5 py-4 text-sm text-gray-600">{{ optional($movimiento->created_at)->format('d/m/Y H:i') }}</td>
                                <td class="px-5 py-4">
                                    <div class="font-bold text-gray-900">{{ optional($movimiento->presentacion?->producto)->nombre_base ?: 'Producto no disponible' }}</div>
                                    <div class="text-xs text-gray-500">{{ optional($movimiento->presentacion)->nombre_variante }}</div>
                                </td>
                                <td class="px-5 py-4 text-sm font-bold {{ $movimiento->cantidad < 0 ? 'text-red-600' : 'text-green-700' }}">
                                    {{ $movimiento->tipo_movimiento }} ({{ $movimiento->cantidad }})
                                </td>
                                <td class="px-5 py-4 text-sm text-gray-700">
                                    {{ $movimiento->stock_anterior }} -> {{ $movimiento->stock_nuevo }}
                                </td>
                                <td class="px-5 py-4 text-sm text-gray-600">{{ $movimiento->motivo ?: 'Sin motivo' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-12 text-center text-gray-500">No hay movimientos de stock web.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@endsection
