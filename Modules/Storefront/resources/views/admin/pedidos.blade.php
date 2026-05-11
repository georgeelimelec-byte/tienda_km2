@extends('layouts.admin')

@section('title', 'Pedidos WhatsApp')
@section('page-title', 'Pedidos WhatsApp')
@section('page-kicker', 'Bandeja operativa de tienda virtual')

@section('topbar-actions')
    <span class="topbar-badge"><i class="fab fa-whatsapp"></i> Pedidos web</span>
@endsection

@section('content')
    <div class="max-w-[1500px] mx-auto w-full space-y-6">
        <div class="grid grid-cols-2 gap-3 md:grid-cols-4 xl:grid-cols-8">
            @foreach($statuses as $status)
                <a href="{{ route('admin.pedidos.index', array_filter(['estado' => $status, 'q' => $search])) }}"
                   class="rounded-lg border px-4 py-3 transition {{ $activeStatus === $status ? 'border-brand bg-orange-50 text-brand' : 'border-gray-200 bg-white text-gray-700 hover:border-orange-200 hover:bg-orange-50/40' }}">
                    <p class="text-xs font-bold uppercase">{{ $status }}</p>
                    <p class="mt-1 text-2xl font-black">{{ (int) ($resumenEstados[$status] ?? 0) }}</p>
                </a>
            @endforeach
        </div>

        <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
            <form method="GET" action="{{ route('admin.pedidos.index') }}" class="grid gap-3 md:grid-cols-[1fr_220px_auto]">
                <input type="search" name="q" value="{{ $search }}" class="rounded-lg border-gray-200 bg-gray-50 px-4 py-2.5 focus:border-brand focus:ring-brand" placeholder="Buscar por codigo, cliente o numero de WhatsApp">
                <select name="estado" class="rounded-lg border-gray-200 bg-gray-50 px-4 py-2.5 focus:border-brand focus:ring-brand">
                    <option value="">Todos los estados</option>
                    @foreach($statuses as $status)
                        <option value="{{ $status }}" @selected($activeStatus === $status)>{{ $status }}</option>
                    @endforeach
                </select>
                <button class="inline-flex items-center justify-center gap-2 rounded-lg bg-gray-900 px-4 py-2.5 font-bold text-white transition hover:bg-brand">
                    <i class="fas fa-search"></i> Filtrar
                </button>
            </form>
        </div>

        <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wide text-gray-500">Pedido</th>
                            <th class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wide text-gray-500">Cliente</th>
                            <th class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wide text-gray-500">Estado</th>
                            <th class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wide text-gray-500">Importe</th>
                            <th class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wide text-gray-500">Atencion</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse($pedidos as $pedido)
                            @php
                                $statusClass = match($pedido->estado) {
                                    'Pendiente' => 'bg-yellow-50 text-yellow-800 border-yellow-200',
                                    'Observado', 'Ajustado' => 'bg-orange-50 text-orange-800 border-orange-200',
                                    'Confirmado', 'En Preparacion', 'En Delivery' => 'bg-blue-50 text-blue-800 border-blue-200',
                                    'Entregado' => 'bg-green-50 text-green-800 border-green-200',
                                    'Cancelado' => 'bg-red-50 text-red-800 border-red-200',
                                    default => 'bg-gray-50 text-gray-700 border-gray-200',
                                };
                                $editable = !in_array($pedido->estado, ['Cancelado', 'Entregado'], true);
                            @endphp
                            <tr class="align-top">
                                <td class="px-5 py-5">
                                    <div class="font-black text-gray-900">{{ $pedido->codigo_pedido }}</div>
                                    <div class="mt-1 text-xs text-gray-500">{{ optional($pedido->created_at)->format('d/m/Y H:i') }}</div>
                                    <a href="{{ route('admin.pedidos.ticket', $pedido->id_pedido_whatsapp) }}" target="_blank" class="mt-3 inline-flex items-center gap-2 rounded-lg border border-gray-200 px-3 py-2 text-xs font-bold text-gray-600 transition hover:border-brand hover:text-brand">
                                        <i class="fas fa-print"></i> Ticket
                                    </a>
                                </td>
                                <td class="px-5 py-5">
                                    <div class="font-bold text-gray-900">{{ $pedido->cliente_nombre }}</div>
                                    <div class="mt-1 text-[11px] font-bold uppercase text-gray-400">Numero de WhatsApp</div>
                                    <a class="mt-1 block text-sm font-bold text-green-700" href="https://wa.me/{{ preg_replace('/\D+/', '', $pedido->cliente_whatsapp) }}" target="_blank">
                                        {{ $pedido->cliente_whatsapp }}
                                    </a>
                                    <div class="mt-2 max-w-sm text-xs leading-relaxed text-gray-500">
                                        {{ $pedido->cliente_direccion }}
                                        @if($pedido->cliente_referencia)
                                            <span class="block">Ref.: {{ $pedido->cliente_referencia }}</span>
                                        @endif
                                        <span class="block">Zona: {{ optional($pedido->zonaDelivery)->nombre ?: 'Sin zona' }}</span>
                                    </div>
                                </td>
                                <td class="px-5 py-5">
                                    <span class="inline-flex rounded-md border px-3 py-1 text-xs font-black {{ $statusClass }}">{{ $pedido->estado }}</span>
                                    <form method="POST" action="{{ route('admin.pedidos.status', $pedido->id_pedido_whatsapp) }}" class="mt-3 space-y-2">
                                        @csrf
                                        <select name="estado" class="w-full rounded-lg border-gray-200 bg-gray-50 px-3 py-2 text-sm focus:border-brand focus:ring-brand">
                                            @foreach($statuses as $status)
                                                <option value="{{ $status }}" @selected($pedido->estado === $status)>{{ $status }}</option>
                                            @endforeach
                                        </select>
                                        <input type="text" name="referencia_atencion" value="{{ $pedido->referencia_atencion }}" class="w-full rounded-lg border-gray-200 bg-gray-50 px-3 py-2 text-sm focus:border-brand focus:ring-brand" placeholder="Ref. interna pago/atencion">
                                        <textarea name="nota_interna" rows="2" class="w-full rounded-lg border-gray-200 bg-gray-50 px-3 py-2 text-sm focus:border-brand focus:ring-brand" placeholder="Nota interna">{{ $pedido->nota_interna }}</textarea>
                                        <button class="w-full rounded-lg bg-brand px-3 py-2 text-sm font-bold text-white transition hover:bg-brand-dark">
                                            Guardar estado
                                        </button>
                                    </form>
                                </td>
                                <td class="px-5 py-5">
                                    <div class="space-y-1 text-sm text-gray-600">
                                        <div class="flex justify-between gap-8"><span>Productos</span><strong>S/ {{ number_format((float) $pedido->total_productos, 2) }}</strong></div>
                                        <div class="flex justify-between gap-8"><span>Delivery</span><strong>S/ {{ number_format((float) $pedido->costo_delivery, 2) }}</strong></div>
                                        <div class="flex justify-between gap-8 border-t border-dashed border-gray-200 pt-2 text-gray-900"><span>Total</span><strong>S/ {{ number_format((float) $pedido->total_pedido, 2) }}</strong></div>
                                    </div>
                                    @if($pedido->whatsapp_url)
                                        <a href="{{ $pedido->whatsapp_url }}" target="_blank" class="mt-4 inline-flex items-center gap-2 rounded-lg bg-green-50 px-3 py-2 text-xs font-bold text-green-700 transition hover:bg-green-100">
                                            <i class="fab fa-whatsapp"></i> Mensaje cliente
                                        </a>
                                    @endif
                                </td>
                                <td class="px-5 py-5">
                                    <div class="text-sm">
                                        <div class="font-bold text-gray-900">{{ optional($pedido->operador)->nombres ?: 'Sin operador asignado' }}</div>
                                        <div class="mt-1 text-xs text-gray-500">El operador confirma cantidades y actualiza este pedido.</div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="5" class="bg-gray-50 px-5 py-4">
                                    <div class="overflow-x-auto rounded-lg border border-gray-200 bg-white">
                                        <table class="min-w-full divide-y divide-gray-100">
                                            <thead class="bg-white">
                                                <tr>
                                                    <th class="px-4 py-3 text-left text-xs font-bold uppercase text-gray-500">Producto</th>
                                                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Solicitado</th>
                                                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-gray-500">Confirmado</th>
                                                    <th class="px-4 py-3 text-right text-xs font-bold uppercase text-gray-500">Subtotal</th>
                                                    <th class="px-4 py-3 text-left text-xs font-bold uppercase text-gray-500">Ajuste</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-100">
                                                @foreach($pedido->detalles as $detalle)
                                                    <tr>
                                                        <td class="px-4 py-3">
                                                            <div class="font-bold text-gray-900">{{ $detalle->nombre_producto }}</div>
                                                            <div class="mt-1 text-xs text-gray-500">
                                                                S/ {{ number_format((float) $detalle->precio_unitario, 2) }}
                                                                @if($detalle->estado_item)
                                                                    <span class="ml-2 rounded bg-gray-100 px-2 py-0.5 font-bold">{{ $detalle->estado_item }}</span>
                                                                @endif
                                                            </div>
                                                            @if($detalle->motivo_ajuste)
                                                                <div class="mt-1 text-xs text-orange-700">Motivo: {{ $detalle->motivo_ajuste }}</div>
                                                            @endif
                                                        </td>
                                                        <td class="px-4 py-3 text-center font-bold text-gray-700">{{ $detalle->cantidad_solicitada }}</td>
                                                        <td class="px-4 py-3 text-center font-bold text-gray-900">{{ $detalle->cantidad_confirmada }}</td>
                                                        <td class="px-4 py-3 text-right font-bold text-gray-900">S/ {{ number_format((float) $detalle->subtotal, 2) }}</td>
                                                        <td class="px-4 py-3">
                                                            <form method="POST" action="{{ route('admin.pedidos.items.adjust', [$pedido->id_pedido_whatsapp, $detalle->id_detalle]) }}" class="grid gap-2 md:grid-cols-[120px_1fr_auto]">
                                                                @csrf
                                                                <input type="number" name="cantidad_confirmada" value="{{ $detalle->cantidad_confirmada }}" min="0" @disabled(!$editable) class="rounded-lg border-gray-200 bg-gray-50 px-3 py-2 text-sm focus:border-brand focus:ring-brand">
                                                                <input type="text" name="motivo_ajuste" value="{{ $detalle->motivo_ajuste }}" required @disabled(!$editable) class="rounded-lg border-gray-200 bg-gray-50 px-3 py-2 text-sm focus:border-brand focus:ring-brand" placeholder="Motivo del ajuste">
                                                                <button @disabled(!$editable) class="rounded-lg bg-gray-900 px-3 py-2 text-sm font-bold text-white transition hover:bg-brand disabled:cursor-not-allowed disabled:opacity-50">
                                                                    Ajustar
                                                                </button>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-16 text-center">
                                    <div class="text-lg font-bold text-gray-700">No hay pedidos para los filtros seleccionados.</div>
                                    <p class="mt-1 text-sm text-gray-500">Los pedidos creados desde la tienda virtual apareceran aqui como una tabla de atencion.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="border-t border-gray-100 px-5 py-4">
                {{ $pedidos->links() }}
            </div>
        </div>
    </div>
@endsection
