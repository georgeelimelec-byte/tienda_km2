@extends('storefront::layouts.master')

@section('content')
<div class="animate-[fadeIn_0.5s_ease-out] py-4 pt-6 max-w-[1680px] mx-auto" x-data="checkoutForm({{ (float) $igv }})">
    <a href="{{ route('storefront.index') }}" class="inline-flex items-center text-gray-500 mb-8 hover:text-brand font-medium transition-colors">
        <div class="bg-white rounded-lg p-2 mr-2 shadow-sm border border-gray-100">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
        </div>
        Volver al Catálogo
    </a>

    <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-start">
        <!-- Formulario (Izquierda) -->
        <div class="md:col-span-7 space-y-6">
            <div class="flex items-center mb-2">
                <span class="bg-brand text-white rounded-full w-8 h-8 flex items-center justify-center font-bold mr-3 shadow-md shadow-brand/30">1</span>
                <h2 class="text-3xl font-extrabold text-gray-900">Confirma tu Pedido</h2>
            </div>
            <p class="text-gray-500 ml-11 mb-6">Completaras el pedido via WhatsApp con tu agente asignado.</p>

            @if(session('error'))
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-r-xl shadow-sm mb-6" role="alert">
                    <div class="flex">
                        <svg class="h-6 w-6 text-red-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span>{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('storefront.store_pedido') }}" x-ref="checkoutForm" @submit="prepareSubmit" class="space-y-6">
                @csrf
                <input type="hidden" name="cart" x-model="cartJson" x-ref="cartInput">
                
                <div class="bg-white p-6 md:p-8 rounded-lg shadow-sm hover:shadow-md transition-shadow border border-gray-200 relative overflow-hidden">
                    <h3 class="text-lg font-bold mb-5 text-gray-900 relative z-10 flex items-center gap-2">
                        <svg class="w-5 h-5 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        Datos Personales
                    </h3>
                    
                    <div class="space-y-5 relative z-10">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5 ml-1">Nombre Completo <span class="text-brand">*</span></label>
                            <input type="text" name="nombre" value="{{ old('nombre', $cliente->nombre_o_razon_social ?? '') }}" required class="w-full border-gray-200 rounded-lg px-4 py-3 bg-gray-50/50 hover:bg-gray-50 focus:bg-white focus:border-brand focus:ring-4 focus:ring-brand/10 transition-all outline-none" placeholder="Tu nombre completo">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5 ml-1">Numero de WhatsApp <span class="text-brand">*</span></label>
                            <div class="flex">
                                <span class="inline-flex items-center px-4 rounded-l-lg border border-r-0 border-gray-200 bg-gray-50 text-gray-500 font-medium">+51</span>
                                <input type="tel" name="numero_whatsapp" value="{{ old('numero_whatsapp', old('whatsapp', $cliente->celular ?? '')) }}" required class="flex-1 w-full border-gray-200 rounded-r-lg px-4 py-3 bg-gray-50/50 hover:bg-gray-50 focus:bg-white focus:border-brand focus:ring-4 focus:ring-brand/10 transition-all outline-none" placeholder="999 888 777">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 md:p-8 rounded-lg shadow-sm hover:shadow-md transition-shadow border border-gray-200 relative overflow-hidden">
                    <h3 class="text-lg font-bold mb-5 text-gray-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        Entrega y Envíos
                    </h3>
                    
                    <div class="space-y-5">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5 ml-1">Zona de Recepción <span class="text-brand">*</span></label>
                            <div class="relative">
                                <select name="id_zona" x-model="selectedZoneId" @change="updateDeliveryCost" required class="w-full border-gray-200 rounded-lg px-4 py-3 bg-gray-50/50 hover:bg-gray-50 focus:bg-white focus:border-brand focus:ring-4 focus:ring-brand/10 transition-all outline-none appearance-none cursor-pointer">
                                    <option value="" disabled>Selecciona tu distrito...</option>
                                    @foreach($zonas as $zona)
                                    <option value="{{ $zona->id_zona }}" data-tarifa="{{ $zona->tarifa }}">{{ $zona->nombre }} (+ S/ {{ number_format($zona->tarifa, 2) }})</option>
                                    @endforeach
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5 ml-1">Dirección Exacta <span class="text-brand">*</span></label>
                            <input type="text" name="direccion" value="{{ old('direccion', $cliente->direccion ?? '') }}" required class="w-full border-gray-200 rounded-lg px-4 py-3 bg-gray-50/50 hover:bg-gray-50 focus:bg-white focus:border-brand focus:ring-4 focus:ring-brand/10 transition-all outline-none" placeholder="Ej. Calle Los Pinos 123, Dpto 4">
                        </div>
                        
                        <div>
                            <label class="block text-sm text-gray-700 mb-1.5 ml-1 font-medium">Referencia (Opcional)</label>
                            <input type="text" name="referencia" class="w-full border-gray-200 rounded-lg px-4 py-3 bg-gray-50/50 hover:bg-gray-50 focus:bg-white focus:border-brand focus:ring-4 focus:ring-brand/10 transition-all outline-none" placeholder="Ej. Frente al parque">
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Resumen (Derecha) -->
        <div class="md:col-span-5 relative">
            <div class="sticky top-24">
                <div class="flex items-center mb-6">
                    <span class="bg-gray-200 text-gray-600 rounded-full w-8 h-8 flex items-center justify-center font-bold mr-3">2</span>
                    <h3 class="text-2xl font-bold text-gray-900">Resumen</h3>
                </div>
                
                <div class="bg-white rounded-lg p-6 shadow-lg shadow-gray-200/40 border border-gray-200 flex flex-col h-full">
                    
                    <div class="flex-1 overflow-y-auto max-h-60 mb-6 space-y-4 pr-2">
                        <template x-for="(item, index) in items" :key="index">
                            <div class="flex gap-3">
                                <img :src="item.image" class="w-14 h-14 rounded-md object-cover border border-gray-200">
                                <div class="flex-1">
                                    <p class="font-semibold text-sm text-gray-800 leading-tight" x-text="item.name"></p>
                                    <p class="text-xs text-gray-400" x-show="item.variant" x-text="item.variant"></p>
                                    <div class="flex justify-between items-center mt-1">
                                        <p class="text-xs text-gray-500" x-text="item.quantity + ' unidad(es)'"></p>
                                        <p class="font-bold text-gray-900" x-text="'S/ ' + Number(item.price * item.quantity).toFixed(2)"></p>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div class="border-t border-gray-100 pt-5 space-y-3">
                        <div class="flex justify-between items-center text-gray-600">
                            <span>Subtotal Carrito</span>
                            <span class="font-semibold" x-text="'S/ ' + Number(subtotal).toFixed(2)"></span>
                        </div>
                        <div class="flex justify-between items-center text-gray-500 text-sm">
                            <span>IGV incluido (<span x-text="Number(igvPercent).toFixed(2)"></span>%)</span>
                            <span x-text="'S/ ' + Number(igvAmount).toFixed(2)"></span>
                        </div>
                        <div class="flex justify-between items-center text-gray-600">
                            <span class="flex items-center gap-1">Flete de Envío <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></span>
                            <span class="font-semibold" x-text="'S/ ' + Number(deliveryCost).toFixed(2)"></span>
                        </div>
                        <div class="border-t border-dashed border-gray-200 my-2"></div>
                        <div class="flex justify-between items-center">
                            <span class="text-lg font-bold text-gray-900">Total a Pagar</span>
                            <span class="text-2xl font-black text-brand" x-text="'S/ ' + (Number(subtotal) + Number(deliveryCost)).toFixed(2)"></span>
                        </div>
                    </div>

                    <button type="button" @click="$refs.checkoutForm.requestSubmit()" :disabled="items.length === 0 || !selectedZoneId" class="mt-8 w-full relative group overflow-hidden rounded-lg flex items-center justify-center py-4 bg-ink text-white font-bold text-lg transition-all hover:bg-brand hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed">
                        <span class="absolute right-0 w-8 h-32 -mt-12 transition-all duration-1000 transform translate-x-12 bg-white opacity-10 rotate-12 group-hover:-translate-x-96 ease"></span>
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86s.274.072.376-.043c.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.045.072.045.419-.099.824zm-3.423-14.416c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm.029 18.88c-1.161 0-2.305-.292-3.318-.844l-3.677.964.984-3.595c-.607-1.052-.927-2.246-.926-3.468.001-5.824 4.74-10.563 10.581-10.563 5.824 0 10.564 4.741 10.564 10.564 0 5.824-4.74 10.564-10.564 10.564z"/></svg>
                        Pedir por WhatsApp
                    </button>
                    <p class="text-center text-xs text-gray-400 mt-4 h-4" x-show="!selectedZoneId">Selecciona una zona de envío para continuar</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('checkoutForm', (igvPercent) => ({
            items: [],
            cartJson: '',
            subtotal: 0,
            deliveryCost: 0,
            selectedZoneId: '',
            igvPercent,
            
            init() {
                this.items = JSON.parse(localStorage.getItem('km2_cart') || '[]');
                this.subtotal = this.items.reduce((acc, item) => acc + (item.price * item.quantity), 0);
            },

            get igvAmount() {
                return Number(this.subtotal) * (Number(this.igvPercent) / (100 + Number(this.igvPercent)));
            },
            
            updateDeliveryCost(e) {
                const selectedOption = e.target.options[e.target.selectedIndex];
                this.deliveryCost = parseFloat(selectedOption.dataset.tarifa || 0);
            },
            
            prepareSubmit(e) {
                if(this.items.length === 0) {
                    e.preventDefault();
                    alert('El carrito esta vacio');
                    return;
                }
                this.cartJson = JSON.stringify(this.items);
                this.$refs.cartInput.value = this.cartJson;
            }
        }))
    });
</script>
<style>
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endsection
