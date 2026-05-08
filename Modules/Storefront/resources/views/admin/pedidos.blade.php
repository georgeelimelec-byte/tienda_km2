@extends('layouts.admin')

@section('title', 'Pedidos WhatsApp')
@section('page-title', 'Pedidos WhatsApp')
@section('page-kicker', 'Tienda virtual')

@section('topbar-actions')
    <span class="topbar-badge"><i class="fab fa-whatsapp"></i> Bandeja en vivo</span>
@endsection

@section('content')
    <div class="max-w-[1400px] mx-auto w-full" x-data="ordersBoard()">
        <div class="overflow-x-auto pb-3">
            <div class="flex gap-6 h-[calc(100vh-210px)] items-start">
                @foreach($statuses as $status)
                    <div class="bg-gray-50 flex-shrink-0 w-80 rounded-xl flex flex-col h-full border border-gray-200">
                        <div class="p-4 border-b border-gray-200 bg-white rounded-t-xl flex justify-between items-center">
                            <h2 class="font-bold text-gray-700">{{ $status }}</h2>
                            <span class="bg-gray-200 text-gray-600 text-xs py-1 px-2 rounded-full font-bold">{{ count($pedidos[$status] ?? []) }}</span>
                        </div>

                        <div class="p-3 flex-1 overflow-y-auto space-y-3">
                            @forelse($pedidos[$status] ?? [] as $pedido)
                                <div class="bg-white border-l-4 @if($status === 'Pendiente') border-yellow-400 @elseif($status === 'Confirmado') border-blue-500 @elseif($status === 'Cancelado') border-red-500 @else border-brand @endif p-3 rounded shadow-sm hover:shadow-md cursor-pointer transition"
                                     @click="openModal({{ $pedido->toJson() }})">
                                    <div class="flex justify-between items-start mb-2">
                                        <span class="font-bold text-gray-800">{{ $pedido->codigo_pedido }}</span>
                                        <span class="text-xs text-gray-500">{{ $pedido->created_at->format('H:i') }}</span>
                                    </div>
                                    <p class="text-sm font-semibold text-gray-700">{{ $pedido->cliente_nombre }}</p>
                                    <p class="text-xs text-gray-500 mt-1">{{ optional($pedido->zonaDelivery)->nombre ?: 'Sin zona' }}</p>

                                    <div class="flex justify-between items-end mt-3 text-xs">
                                        <span class="text-gray-500">Total: S/ {{ number_format((float) $pedido->total_pedido, 2) }}</span>
                                        @if($pedido->comprobante_referencia)
                                            <span class="bg-gray-100 text-gray-600 px-2 py-0.5 rounded border border-gray-200">{{ $pedido->comprobante_referencia }}</span>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="text-center text-xs text-gray-400 py-4">No hay pedidos</div>
                            @endforelse
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div x-show="modalOpen" style="display: none;" class="fixed inset-0 z-[200] flex items-center justify-center">
            <div x-show="modalOpen" @click="modalOpen = false" class="fixed inset-0 bg-black opacity-50"></div>
            <div class="bg-white rounded-xl shadow-2xl z-10 w-full max-w-lg overflow-hidden flex flex-col max-h-[90vh]">
                <div class="p-5 border-b flex justify-between items-center bg-gray-50">
                    <h3 class="text-xl font-bold" x-text="activeOrder?.codigo_pedido"></h3>
                    <button @click="modalOpen = false" class="text-gray-500 hover:text-black">&times;</button>
                </div>

                <div class="p-5 flex-1 overflow-y-auto">
                    <div class="grid grid-cols-2 gap-4 mb-4 text-sm">
                        <div>
                            <p class="text-gray-500">Cliente</p>
                            <p class="font-bold text-gray-800" x-text="activeOrder?.cliente_nombre"></p>
                        </div>
                        <div>
                            <p class="text-gray-500">WhatsApp</p>
                            <a class="font-bold text-green-700" :href="'https://wa.me/' + normalizePhone(activeOrder?.cliente_whatsapp)" target="_blank" x-text="activeOrder?.cliente_whatsapp"></a>
                        </div>
                        <div class="col-span-2">
                            <p class="text-gray-500">Direccion</p>
                            <p class="text-gray-800" x-text="activeOrder?.cliente_direccion + (activeOrder?.cliente_referencia ? ' (' + activeOrder.cliente_referencia + ')' : '')"></p>
                        </div>
                    </div>

                    <div class="bg-gray-50 rounded p-3 mb-4 border">
                        <p class="text-xs font-bold text-gray-500 uppercase mb-2">Productos</p>
                        <ul class="text-sm space-y-2 mb-3">
                            <template x-for="item in activeOrder?.detalles" :key="item.id_detalle">
                                <li class="flex justify-between">
                                    <span x-text="item.cantidad + 'x ' + item.nombre_producto"></span>
                                    <span class="font-medium" x-text="'S/ ' + Number(item.subtotal).toFixed(2)"></span>
                                </li>
                            </template>
                        </ul>
                        <div class="border-t pt-2 text-sm space-y-1">
                            <div class="flex justify-between"><span>Productos</span><strong x-text="'S/ ' + Number(activeOrder?.total_productos || 0).toFixed(2)"></strong></div>
                            <div class="flex justify-between"><span>Delivery</span><strong x-text="'S/ ' + Number(activeOrder?.costo_delivery || 0).toFixed(2)"></strong></div>
                            <div class="flex justify-between font-bold text-gray-900"><span>Total</span><strong x-text="'S/ ' + Number(activeOrder?.total_pedido || 0).toFixed(2)"></strong></div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-bold text-gray-700 mb-1">Referencia de pago o comprobante</label>
                        <input type="text" x-model="referenceInput" class="w-full border-gray-300 rounded px-3 py-2 bg-gray-50 focus:bg-white focus:ring-brand focus:border-brand" placeholder="Ej. Yape 123456">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-bold text-gray-700 mb-1">Nota interna</label>
                        <textarea x-model="noteInput" class="w-full border-gray-300 rounded px-3 py-2 bg-gray-50 focus:bg-white focus:ring-brand focus:border-brand" rows="2" placeholder="Indicaciones del cliente o despacho"></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Mover a estado:</label>
                        <div class="grid grid-cols-2 gap-2">
                            @foreach($statuses as $status)
                                <button @click="updateStatus('{{ $status }}')" class="py-2 px-3 bg-brand/10 text-brand font-semibold rounded hover:bg-brand hover:text-white border border-brand/30 transition">{{ $status }}</button>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="p-4 bg-gray-50 border-t flex justify-between items-center">
                    <a :href="'/admin/pedidos/' + activeOrder?.id_pedido_whatsapp + '/ticket'" target="_blank" class="text-sm font-semibold text-gray-600 hover:text-black flex items-center gap-1">
                        <i class="fas fa-print"></i> Ticket de despacho
                    </a>
                    <a x-show="activeOrder?.whatsapp_url" :href="activeOrder?.whatsapp_url" target="_blank" class="text-sm font-semibold text-green-700 hover:text-green-900 flex items-center gap-1">
                        <i class="fab fa-whatsapp"></i> Abrir mensaje
                    </a>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('ordersBoard', () => ({
                    modalOpen: false,
                    activeOrder: null,
                    referenceInput: '',
                    noteInput: '',

                    openModal(order) {
                        this.activeOrder = order;
                        this.referenceInput = order.comprobante_referencia || '';
                        this.noteInput = order.nota_interna || '';
                        this.modalOpen = true;
                    },

                    normalizePhone(phone) {
                        return String(phone || '').replace(/\D/g, '');
                    },

                    updateStatus(status) {
                        fetch(`/admin/pedidos/${this.activeOrder.id_pedido_whatsapp}/status`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                estado: status,
                                comprobante_referencia: this.referenceInput,
                                nota_interna: this.noteInput
                            })
                        })
                            .then(res => res.json())
                            .then(data => {
                                if (data.success) {
                                    window.location.reload();
                                }
                            });
                    }
                }))
            });
        </script>
    </div>
@endsection
