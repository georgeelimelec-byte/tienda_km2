@extends('storefront::layouts.master')

@section('content')
@php
    $imgPrincipal = $producto->imagen_principal_url;
    $baseImages = $producto->imagenes
        ->map(fn ($image) => $image->url)
        ->filter()
        ->values();

    if ($baseImages->isEmpty()) {
        $baseImages = collect([$imgPrincipal]);
    }

    $presentacionesData = $producto->presentaciones->map(function ($presentacion) use ($producto, $baseImages) {
        $variantImages = $presentacion->imagenes
            ->map(fn ($image) => $image->url)
            ->filter()
            ->values();
        $images = $variantImages->isNotEmpty() ? $variantImages : $baseImages;

        $price = (float) $presentacion->precio_efectivo;
        $basePrice = (float) $presentacion->precio;
        $referencePrice = $presentacion->precio_referencial !== null ? (float) $presentacion->precio_referencial : null;
        $displayReference = $referencePrice && $referencePrice > $price
            ? $referencePrice
            : (($presentacion->tiene_promocion && $basePrice > $price) ? $basePrice : null);

        return [
            'id' => $presentacion->id_presentacion,
            'presentation_id' => $presentacion->id_presentacion,
            'product_id' => $producto->id_producto,
            'name' => $producto->nombre_base,
            'variant' => $presentacion->nombre_variante,
            'price' => $price,
            'regular_price' => $displayReference,
            'has_offer' => $displayReference !== null,
            'has_promotion' => (bool) $presentacion->tiene_promocion,
            'stock' => (int) $presentacion->stock_web,
            'image' => $images->first(),
            'images' => $images->values(),
        ];
    })->values();
    $category = $producto->categoria;
    $categoryName = $category->nombre ?? 'Sin categoria';
    $parentName = $category && $category->padre ? $category->padre->nombre : $categoryName;
    $categoryContext = strtolower($parentName . ' ' . $categoryName);
    $isCafe = str_contains($categoryContext, 'cafe') || str_contains($categoryContext, 'cafeteria') || str_contains($categoryContext, 'panaderia') || str_contains($categoryContext, 'sandwich');
@endphp

<div class="animate-[fadeIn_0.5s_ease-out] pt-6 max-w-[1680px] mx-auto" x-data="productDetail(@js($presentacionesData), @js($imgPrincipal), {{ (float) $igv }})">
    <nav class="mb-6 flex items-center text-sm font-medium text-gray-500">
        <a href="{{ route('storefront.index') }}" class="hover:text-brand transition-colors flex items-center">
            <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
            Vitrina
        </a>
        @if($producto->categoria)
            <span class="mx-2 text-gray-300">/</span>
            <a href="{{ route('storefront.index', ['categoria_id' => $producto->id_categoria]) }}" class="hover:text-brand transition-colors">{{ $categoryName }}</a>
        @endif
        <span class="mx-2 text-gray-300">/</span>
        <span class="text-gray-900 truncate max-w-xs">{{ $producto->nombre_base }}</span>
    </nav>

    <section class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden mb-12">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-0 relative">
            <div class="relative bg-gray-100 overflow-hidden flex flex-col min-h-[520px]">
                <img :src="currentImage" alt="{{ $producto->nombre_base }}" class="absolute inset-0 w-full h-full object-cover cursor-zoom-in transition-all duration-500 transform hover:scale-105">
                <div class="absolute inset-0 bg-gradient-to-t from-gray-950/50 via-transparent to-transparent z-10 pointer-events-none"></div>
                <span class="absolute top-5 left-5 z-20 px-3 py-1 rounded-md text-xs font-bold border {{ $isCafe ? 'bg-orange-50/95 text-coffee border-orange-100' : 'bg-white/95 text-gray-700 border-gray-200' }}">{{ $isCafe ? 'Cafe' : 'Minimarket' }}</span>

                <template x-if="currentImages.length > 1">
                    <div class="absolute left-0 right-0 bottom-6 flex gap-4 z-20 w-full justify-center flex-wrap px-6">
                        <template x-for="image in currentImages" :key="image">
                            <button @click="currentImage = image" class="w-16 h-16 rounded-lg border-2 overflow-hidden transition-all bg-white" :class="currentImage === image ? 'border-brand shadow-md scale-110' : 'border-white/70 hover:border-brand/70'">
                                <img :src="image" alt="{{ $producto->nombre_base }}" class="w-full h-full object-cover">
                            </button>
                        </template>
                    </div>
                </template>
            </div>

            <div class="p-8 lg:p-12 flex flex-col relative z-10">
                <div class="flex flex-wrap items-center gap-2 mb-4">
                    <template x-if="selectedPresentation && selectedPresentation.stock > 0">
                        <span class="px-3 py-1 bg-gray-100 text-gray-700 text-xs font-bold uppercase rounded-md flex items-center"><span class="w-2 h-2 bg-brand rounded-full mr-1.5"></span> En stock</span>
                    </template>
                    <template x-if="selectedPresentation && selectedPresentation.stock <= 0">
                        <span class="px-3 py-1 bg-red-100 text-red-700 text-xs font-bold uppercase rounded-md flex items-center"><span class="w-2 h-2 bg-red-500 rounded-full mr-1.5"></span> Agotado</span>
                    </template>
                    <span class="text-sm text-gray-400 font-medium">{{ $parentName }} / {{ $categoryName }}</span>
                    <span class="text-sm text-gray-500">Pedido por WhatsApp</span>
                </div>

                <h1 class="text-3xl lg:text-4xl font-extrabold text-gray-900 leading-tight mb-4">
                    {{ $producto->nombre_base }}
                </h1>

                <div class="flex items-baseline gap-3 mb-2">
                    <span class="text-4xl font-black text-brand" x-text="'S/ ' + Number(selectedPresentation?.price || 0).toFixed(2)"></span>
                    <template x-if="selectedPresentation && selectedPresentation.has_offer">
                        <span class="text-lg text-gray-400 font-medium line-through" x-text="'S/ ' + Number(selectedPresentation.regular_price).toFixed(2)"></span>
                    </template>
                </div>
                <p class="text-sm text-gray-500 mb-6">
                    Incluye IGV {{ number_format($igv, 2) }}%:
                    <span x-text="'S/ ' + Number(igvAmount).toFixed(2)"></span>
                </p>

                @if($producto->presentaciones->count() > 1)
                    <div class="mb-6">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Presentacion</label>
                        <select x-model.number="selectedId" class="w-full border-gray-200 rounded-lg px-4 py-3 bg-gray-50 hover:bg-white focus:border-brand focus:ring-4 focus:ring-brand/10 transition-all outline-none">
                            <template x-for="presentation in presentations" :key="presentation.id">
                                <option :value="presentation.id" x-text="presentation.variant + ' - S/ ' + Number(presentation.price).toFixed(2) + ' (' + presentation.stock + ' disp.)'"></option>
                            </template>
                        </select>
                    </div>
                @endif

                <div class="prose prose-gray max-w-none text-gray-600 mb-8 leading-relaxed">
                    {{ $producto->descripcion ?: 'Producto disponible en la vitrina de minimarket y cafeteria. Revisa presentaciones, precio, stock y envia tu pedido por WhatsApp.' }}
                </div>

                <div class="mt-auto pt-6 border-t border-gray-100 space-y-4">
                    <div class="flex flex-wrap items-center gap-4">
                        <label class="font-bold text-gray-700">Cantidad</label>
                        <div class="flex items-center bg-gray-50 border border-gray-200 rounded-lg p-1 h-12">
                            <button @click="if(cantidad > 1) cantidad--" class="w-10 h-full flex items-center justify-center text-gray-600 hover:bg-white hover:shadow-sm rounded-lg transition-all text-xl font-bold">&minus;</button>
                            <input type="number" x-model.number="cantidad" class="w-12 h-full text-center font-bold text-gray-900 bg-transparent border-none outline-none appearance-none" min="1" :max="selectedPresentation?.stock || 1">
                            <button @click="if(selectedPresentation && cantidad < selectedPresentation.stock) cantidad++" class="w-10 h-full flex items-center justify-center text-gray-600 hover:bg-white hover:shadow-sm rounded-lg transition-all text-xl font-bold">&plus;</button>
                        </div>
                        <span class="text-sm text-gray-500" x-text="selectedPresentation ? '(' + selectedPresentation.stock + ' disponibles)' : ''"></span>
                    </div>

                    <button @click="addToCart(cartPayload)" :disabled="!selectedPresentation || selectedPresentation.stock <= 0" class="w-full relative group overflow-hidden rounded-lg flex items-center justify-center py-4 bg-ink text-white font-bold text-lg shadow-lg shadow-gray-900/20 transition-all hover:bg-brand hover:-translate-y-1 disabled:opacity-50 disabled:cursor-not-allowed">
                        <span class="absolute right-0 w-8 h-32 -mt-12 transition-all duration-1000 transform translate-x-12 bg-white opacity-10 rotate-12 group-hover:-translate-x-96 ease"></span>
                        <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
                        Anadir a mi pedido
                    </button>

                    <button onclick="window.location='{{ route('storefront.checkout') }}'" :disabled="totalItems === 0" class="w-full flex items-center justify-center py-3 text-brand font-bold hover:bg-orange-50 rounded-lg transition-colors disabled:opacity-50">
                        Ir al pedido
                    </button>
                </div>
            </div>
        </div>
    </section>

    @if($relacionados->count() > 0)
        <section>
            <h2 class="text-2xl font-extrabold text-gray-900 mb-6">Para completar tu pedido</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 2xl:grid-cols-5 gap-5">
                @foreach($relacionados as $rel)
                    @php
                        $relPresentacion = $rel->presentaciones->first();
                        if (!$relPresentacion) {
                            continue;
                        }
                        $imgRel = optional($relPresentacion->imagenes->first())->url ?: $rel->imagen_principal_url;
                    @endphp
                    <article class="group bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden flex flex-col transition-all duration-300 hover:shadow-xl hover:-translate-y-1 cursor-pointer" onclick="window.location='{{ route('storefront.producto', $rel->id_producto) }}'">
                        <div class="h-40 bg-gray-100 overflow-hidden relative flex items-center justify-center">
                            <img src="{{ $imgRel }}" alt="{{ $rel->nombre_base }}" class="w-full h-full object-cover hover:scale-105 transition-transform duration-500">
                        </div>
                        <div class="p-4 flex flex-col flex-1">
                            <h3 class="font-bold text-gray-800 text-sm mb-1">{{ $rel->nombre_base }}</h3>
                            <p class="text-xs text-gray-500 mb-2">{{ $relPresentacion->nombre_variante }}</p>
                            <p class="text-brand font-black mt-auto">S/ {{ number_format($relPresentacion->precio_efectivo, 2) }}</p>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
    @endif
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('productDetail', (presentations, initialImage, igvPercent) => ({
            presentations,
            selectedId: presentations[0]?.id || null,
            cantidad: 1,
            currentImage: initialImage,
            igvPercent,
            init() {
                this.syncPresentationImage();
                this.$watch('selectedId', () => {
                    this.cantidad = 1;
                    this.syncPresentationImage();
                });
            },
            get selectedPresentation() {
                return this.presentations.find((presentation) => presentation.id === this.selectedId) || this.presentations[0] || null;
            },
            get currentImages() {
                return this.selectedPresentation?.images?.length ? this.selectedPresentation.images : [initialImage];
            },
            syncPresentationImage() {
                this.currentImage = this.currentImages[0] || initialImage;
            },
            get igvAmount() {
                if (!this.selectedPresentation) {
                    return 0;
                }
                return Number(this.selectedPresentation.price) * (Number(this.igvPercent) / (100 + Number(this.igvPercent)));
            },
            get cartPayload() {
                const quantity = Math.min(Number(this.cantidad || 1), Number(this.selectedPresentation.stock));
                return {
                    ...this.selectedPresentation,
                    image: this.currentImage,
                    quantity,
                    max_stock: Number(this.selectedPresentation.stock),
                };
            },
        }));
    });
</script>
@endsection
