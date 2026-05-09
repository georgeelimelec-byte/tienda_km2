@extends('storefront::layouts.master')

@section('content')
@php
    $selectedCategory = request('categoria_id');
    $activeFilter = request('filtro');
    $bannerCopy = [
        [
            'badge' => 'Cafe y panaderia',
            'subtitle' => 'Cafe preparado al momento, panes y postres de vitrina para recoger o pedir por delivery.',
            'cta' => 'Ver cafeteria',
        ],
        [
            'badge' => 'Despensa inteligente',
            'subtitle' => 'Abarrotes, lacteos y basicos con stock visible, precios claros e IGV incluido.',
            'cta' => 'Comprar basicos',
        ],
        [
            'badge' => 'Snacks y bebidas',
            'subtitle' => 'Bebidas frias, snacks y pedidos rapidos para oficina, casa o camino.',
            'cta' => 'Ver snacks',
        ],
        [
            'badge' => 'Promociones del dia',
            'subtitle' => 'Combos calientes y productos seleccionados para completar tu pedido en minutos.',
            'cta' => 'Ver combos',
        ],
    ];
    $bannerSlides = ($bannersCarrusel ?? collect())->values()->map(function ($banner, $index) use ($bannerCopy) {
        $copy = $bannerCopy[$index % count($bannerCopy)];

        return [
            'title' => $banner->titulo ?: 'Minimarket y cafeteria KM2',
            'image' => $banner->imagen_url,
            'link' => $banner->link_destino ?: route('storefront.index'),
            'badge' => $copy['badge'],
            'subtitle' => $copy['subtitle'],
            'cta' => $copy['cta'],
        ];
    })->values()->all();
    $hasBannerSlides = count($bannerSlides) > 0;
    $promoBanners = ($bannersPromocionales ?? collect())->values();
    $lateralBanners = $promoBanners->where('posicion', 'Lateral')->values();
    $popupBanners = $promoBanners->where('posicion', 'Pop_up')->values();
    $hasActiveOffers = ($promociones ?? collect())->isNotEmpty() || $promoBanners->isNotEmpty();
@endphp

@if($hasBannerSlides)
<style>
    @keyframes km2HeroProgress {
        from { transform: scaleX(0); }
        to { transform: scaleX(1); }
    }

    @keyframes km2HeroDrift {
        from { transform: scale(1.04) translate3d(0, 0, 0); }
        to { transform: scale(1.1) translate3d(-1.5%, -1%, 0); }
    }

    .km2-hero-progress {
        animation: km2HeroProgress linear forwards;
        transform-origin: left center;
    }

    .km2-hero-progress.is-paused {
        animation-play-state: paused;
    }

    .km2-hero-image-active {
        animation: km2HeroDrift 5s ease-out forwards;
    }

    @media (prefers-reduced-motion: reduce) {
        .km2-hero-progress,
        .km2-hero-image-active {
            animation: none;
        }
    }
</style>
@endif

<div class="animate-[fadeIn_0.5s_ease-out] max-w-[1680px] mx-auto">
    @if($hasBannerSlides)
    <section
        class="group relative -mx-4 mb-8 h-[clamp(360px,30vw,500px)] overflow-hidden bg-ink shadow-sm sm:-mx-6 lg:-mx-10"
        x-data="{
            slides: @js($bannerSlides),
            activeIndex: 0,
            interval: 5000,
            timer: null,
            paused: false,
            touchStartX: 0,
            get active() { return this.slides[this.activeIndex] || this.slides[0]; },
            init() { this.play(); },
            play() {
                if (this.slides.length <= 1) return;
                this.stop();
                this.timer = setInterval(() => {
                    if (!this.paused) this.next();
                }, this.interval);
            },
            stop() {
                if (this.timer) {
                    clearInterval(this.timer);
                    this.timer = null;
                }
            },
            restart() {
                this.stop();
                this.play();
            },
            next() {
                this.activeIndex = (this.activeIndex + 1) % this.slides.length;
                this.restart();
            },
            prev() {
                this.activeIndex = (this.activeIndex + this.slides.length - 1) % this.slides.length;
                this.restart();
            },
            go(index) {
                if (index === this.activeIndex) return;
                this.activeIndex = index;
                this.restart();
            },
            handleSwipe(endX) {
                const diff = this.touchStartX - endX;
                if (Math.abs(diff) < 50) return;
                diff > 0 ? this.next() : this.prev();
            }
        }"
        x-init="init()"
        @mouseenter="paused = true"
        @mouseleave="paused = false"
        @touchstart.passive="touchStartX = $event.changedTouches[0].screenX"
        @touchend.passive="handleSwipe($event.changedTouches[0].screenX)"
    >
        <template x-for="(slide, index) in slides" :key="slide.image">
            <div
                x-show="activeIndex === index"
                x-transition:enter="transition ease-out duration-700"
                x-transition:enter-start="opacity-0 scale-105"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-700"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="absolute inset-0"
            >
                <img
                    :src="slide.image"
                    :alt="slide.title"
                    class="h-full w-full object-cover opacity-100"
                    :class="activeIndex === index ? 'km2-hero-image-active' : ''"
                >
            </div>
        </template>
        <div class="absolute inset-0 z-10 bg-gradient-to-r from-ink-dark/68 via-ink-dark/24 to-transparent"></div>
        <div class="absolute inset-x-0 bottom-0 z-10 h-48 bg-gradient-to-t from-ink-dark/60 to-transparent"></div>
        <div class="absolute inset-x-0 top-0 z-10 h-28 bg-gradient-to-b from-ink-dark/25 to-transparent"></div>

        <div class="relative z-20 flex h-full items-end px-6 pb-20 pt-8 sm:px-10 md:px-14 lg:px-16">
            <template x-for="(slide, index) in slides" :key="slide.title">
                <div
                    x-show="activeIndex === index"
                    x-transition:enter="transition ease-out duration-700 delay-100"
                    x-transition:enter-start="opacity-0 translate-y-6"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-300"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 -translate-y-3"
                    class="max-w-[820px]"
                >
                    <span class="mb-4 inline-flex w-fit rounded-md border border-white/25 bg-white/15 px-3 py-1 text-xs font-bold uppercase tracking-wide text-orange-100 backdrop-blur-sm" x-text="slide.badge"></span>
                    <h1 class="mb-4 text-4xl font-black leading-[0.98] tracking-tight text-white drop-shadow md:text-5xl xl:text-6xl" x-text="slide.title"></h1>
                    <p class="max-w-2xl text-base font-medium leading-relaxed text-orange-50 drop-shadow-sm md:text-lg" x-text="slide.subtitle"></p>
                </div>
            </template>
        </div>

        <div class="absolute bottom-5 left-6 right-6 z-30 flex items-end gap-4 sm:left-10 sm:right-10 md:left-14 md:right-14 lg:left-16 lg:right-16">
            <div class="min-w-0 flex-1">
                <div class="mb-3 flex items-center justify-between gap-4">
                    <p class="hidden text-xs font-bold uppercase tracking-wide text-white/75 sm:block">
                        <span x-text="String(activeIndex + 1).padStart(2, '0')"></span>
                        <span class="mx-2 text-white/35">/</span>
                        <span x-text="String(slides.length).padStart(2, '0')"></span>
                    </p>
                </div>
                <div class="flex min-w-0 items-center gap-2">
                <template x-for="(slide, index) in slides" :key="slide.title">
                    <button
                        type="button"
                        @click="go(index)"
                        class="relative h-2 min-w-8 flex-1 overflow-hidden rounded-full bg-white/30 transition hover:bg-white/45 md:max-w-[180px]"
                        :aria-label="'Ir al banner ' + (index + 1)"
                    >
                        <span
                            x-show="activeIndex === index"
                            class="km2-hero-progress absolute inset-y-0 left-0 w-full bg-brand"
                            :class="paused ? 'is-paused' : ''"
                            :style="'animation-duration: ' + interval + 'ms'"
                        ></span>
                    </button>
                </template>
                </div>
            </div>

            <div class="hidden flex-shrink-0 items-center gap-2 md:flex">
                <button
                    type="button"
                    x-show="slides.length > 1"
                    @click="prev()"
                    class="flex h-11 w-11 items-center justify-center rounded-lg border border-white/20 bg-white/10 text-white backdrop-blur transition hover:bg-white/20"
                    aria-label="Banner anterior"
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                </button>

                <button
                    type="button"
                    x-show="slides.length > 1"
                    @click="paused = !paused"
                    class="flex h-11 w-11 items-center justify-center rounded-lg border border-white/20 bg-white/10 text-white backdrop-blur transition hover:bg-white/20"
                    :aria-label="paused ? 'Reanudar carrusel' : 'Pausar carrusel'"
                >
                    <svg x-show="!paused" class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M7 5h3v14H7V5Zm7 0h3v14h-3V5Z"></path></svg>
                    <svg x-cloak x-show="paused" class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7L8 5Z"></path></svg>
                </button>

                <button
                    type="button"
                    x-show="slides.length > 1"
                    @click="next()"
                    class="flex h-11 w-11 items-center justify-center rounded-lg border border-white/20 bg-white/10 text-white backdrop-blur transition hover:bg-white/20"
                    aria-label="Banner siguiente"
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </button>
            </div>
        </div>
    </section>
    @endif

    @if($hasActiveOffers)
        <section class="mb-8">
            <div class="mb-4 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h2 class="text-2xl font-extrabold text-gray-900">Ofertas activas</h2>
                    <p class="text-sm text-gray-500">Promociones y banners vigentes para pedidos rapidos.</p>
                </div>
                <a href="{{ route('storefront.index', ['filtro' => 'promociones']) }}" class="inline-flex w-fit items-center rounded-lg bg-orange-50 px-3 py-2 text-sm font-bold text-brand transition hover:bg-brand hover:text-white">
                    Ver promociones
                    <svg class="ml-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </a>
            </div>

            <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
                @foreach($lateralBanners as $banner)
                    <a href="{{ $banner->link_destino ?: route('storefront.index') }}" class="group relative min-h-[180px] overflow-hidden rounded-lg border border-gray-200 bg-gray-900 shadow-sm lg:min-h-[220px]">
                        <img src="{{ $banner->imagen_url }}" alt="{{ $banner->titulo }}" class="absolute inset-0 h-full w-full object-cover transition duration-500 group-hover:scale-105">
                        <span class="absolute inset-0 bg-gradient-to-t from-gray-950/70 via-gray-950/20 to-transparent"></span>
                        <span class="absolute bottom-4 left-4 right-4 text-lg font-extrabold text-white drop-shadow">{{ $banner->titulo }}</span>
                    </a>
                @endforeach

                @foreach(($promociones ?? collect())->take(3) as $promo)
                    @php
                        $promoProduct = $promo->productos->first();
                        $promoImage = optional($promoProduct?->imagenes->first())->url ?: optional($promoProduct)->imagen_principal_url;
                        $promoScope = $promo->productos->isNotEmpty()
                            ? $promo->productos->count() . ' producto(s)'
                            : $promo->categorias->pluck('nombre')->implode(', ');
                        $discountLabel = $promo->tipo_descuento === 'Monto'
                            ? 'S/ ' . number_format((float) $promo->valor_descuento, 2)
                            : number_format((float) $promo->valor_descuento, 0) . '%';
                    @endphp
                    <a href="{{ route('storefront.index', ['filtro' => 'promociones']) }}" class="group flex min-h-[180px] overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm transition hover:shadow-lg lg:min-h-[220px]">
                        <div class="w-2/5 bg-gray-100">
                            @if($promoImage)
                                <img src="{{ $promoImage }}" alt="{{ $promo->nombre }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                            @endif
                        </div>
                        <div class="flex flex-1 flex-col justify-center p-4">
                            <p class="mb-1 text-xs font-bold uppercase tracking-wide text-brand">Promocion</p>
                            <h3 class="text-base font-extrabold leading-tight text-gray-900">{{ $promo->nombre }}</h3>
                            <p class="mt-2 text-sm text-gray-500">{{ $promoScope ?: 'Productos seleccionados' }}</p>
                            <div class="mt-3 text-xl font-black text-brand">-{{ $discountLabel }}</div>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    @if($popupBanners->isNotEmpty())
        @php
            $popupBanner = $popupBanners->first();
        @endphp
        <div
            x-data="{ open: false, init() { if (window.sessionStorage && !sessionStorage.getItem('km2_popup_seen')) { this.open = true; sessionStorage.setItem('km2_popup_seen', '1'); } } }"
            x-init="init()"
            x-show="open"
            x-cloak
            class="fixed inset-0 z-[90] flex items-center justify-center bg-gray-950/60 px-4"
        >
            <div class="relative w-full max-w-lg overflow-hidden rounded-lg bg-white shadow-2xl">
                <button type="button" @click="open = false" class="absolute right-3 top-3 z-10 flex h-9 w-9 items-center justify-center rounded-lg bg-white/90 text-gray-700 shadow hover:bg-white" aria-label="Cerrar promocion">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 18 6M6 6l12 12"></path></svg>
                </button>
                <a href="{{ $popupBanner->link_destino ?: route('storefront.index') }}" class="block">
                    <img src="{{ $popupBanner->imagen_url }}" alt="{{ $popupBanner->titulo }}" class="h-auto w-full">
                    <div class="p-4">
                        <p class="text-xs font-bold uppercase tracking-wide text-brand">Promocion</p>
                        <h3 class="text-xl font-extrabold text-gray-900">{{ $popupBanner->titulo }}</h3>
                    </div>
                </a>
            </div>
        </div>
    @endif

    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 mb-4">
        <div>
            <h2 class="text-3xl font-extrabold text-gray-900">
                @if($activeFilter === 'promociones')
                    Promociones KM2
                @elseif($activeFilter === 'combos')
                    Combos KM2
                @else
                    Vitrina KM2
                @endif
            </h2>
            <p class="text-gray-500 mt-1">{{ $productos->count() }} producto(s) disponible(s) entre cafeteria y minimarket</p>
            @if($activeFilter)
                <a href="{{ route('storefront.index') }}" class="mt-3 inline-flex items-center rounded-lg bg-orange-50 px-3 py-2 text-sm font-bold text-brand transition hover:bg-brand hover:text-white">
                    Limpiar filtro
                    <svg class="ml-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </a>
            @endif
        </div>
    </div>

    <div class="flex overflow-x-auto hide-scrollbar gap-3 pb-6 mb-2 -mx-4 px-4 sm:mx-0 sm:px-0">
        <a href="{{ route('storefront.index', array_filter(['q' => request('q')])) }}" class="flex-none px-5 py-2.5 rounded-lg font-bold text-sm transition-all shadow-sm border whitespace-nowrap {{ !$selectedCategory ? 'bg-ink text-white border-transparent' : 'bg-white text-gray-600 border-gray-200 hover:border-gray-300 hover:bg-gray-50' }}">
            Todos
        </a>
        @foreach($categoriasTree as $root)
            <a href="{{ route('storefront.index', array_filter(['categoria_id' => $root->id_categoria, 'q' => request('q')])) }}" class="flex-none px-5 py-2.5 rounded-lg font-bold text-sm transition-all shadow-sm border whitespace-nowrap {{ $selectedCategory == $root->id_categoria ? 'bg-coffee text-white border-transparent' : 'bg-white text-gray-700 border-gray-200 hover:border-gray-300 hover:bg-gray-50' }}">
                {{ $root->nombre }}
            </a>
            @foreach($root->hijos as $child)
                <a href="{{ route('storefront.index', array_filter(['categoria_id' => $child->id_categoria, 'q' => request('q')])) }}" class="flex-none px-4 py-2.5 rounded-lg font-semibold text-sm transition-all shadow-sm border whitespace-nowrap {{ $selectedCategory == $child->id_categoria ? 'bg-brand text-white border-transparent' : 'bg-white text-gray-600 border-gray-200 hover:border-gray-300 hover:bg-gray-50' }}">
                    {{ $child->nombre }}
                </a>
                @foreach($child->hijos as $grandChild)
                    <a href="{{ route('storefront.index', array_filter(['categoria_id' => $grandChild->id_categoria, 'q' => request('q')])) }}" class="flex-none px-4 py-2.5 rounded-lg font-medium text-sm transition-all shadow-sm border whitespace-nowrap {{ $selectedCategory == $grandChild->id_categoria ? 'bg-brand text-white border-transparent' : 'bg-white text-gray-500 border-gray-200 hover:border-gray-300 hover:bg-gray-50' }}">
                        {{ $grandChild->nombre }}
                    </a>
                @endforeach
            @endforeach
        @endforeach
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 2xl:grid-cols-5 gap-5">
        @forelse($productos as $producto)
            @php
                $presentacion = $producto->presentaciones->first();
                if (!$presentacion) {
                    continue;
                }

                $img = optional($presentacion->imagenes->first())->url ?: $producto->imagen_principal_url;
                $price = (float) $presentacion->precio_efectivo;
                $basePrice = (float) $presentacion->precio;
                $referencePrice = $presentacion->precio_referencial !== null ? (float) $presentacion->precio_referencial : null;
                $regularPrice = $referencePrice && $referencePrice > $price
                    ? $referencePrice
                    : (($presentacion->tiene_promocion && $basePrice > $price) ? $basePrice : null);
                $stockWeb = (int) $presentacion->stock_web;
                $igvAmount = $igv > 0 ? $price * ($igv / (100 + $igv)) : 0;
                $category = $producto->categoria;
                $categoryName = $category->nombre ?? 'Sin categoria';
                $parentName = $category && $category->padre ? $category->padre->nombre : $categoryName;
                $categoryContext = strtolower($parentName . ' ' . $categoryName);
                $isCafe = str_contains($categoryContext, 'cafe') || str_contains($categoryContext, 'cafeteria') || str_contains($categoryContext, 'panaderia') || str_contains($categoryContext, 'sandwich');
                $cartPayload = [
                    'id' => $presentacion->id_presentacion,
                    'presentation_id' => $presentacion->id_presentacion,
                    'product_id' => $producto->id_producto,
                    'name' => $producto->nombre_base,
                    'variant' => $presentacion->nombre_variante,
                    'price' => $price,
                    'image' => $img,
                    'max_stock' => $stockWeb,
                ];
            @endphp

            <article class="group bg-white rounded-lg shadow-sm hover:shadow-xl border border-gray-200 overflow-hidden flex flex-col transition-all duration-300 transform hover:-translate-y-1">
                <div class="relative h-56 bg-gray-100 overflow-hidden">
                    <img src="{{ $img }}" alt="{{ $producto->nombre_base }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 relative z-10 cursor-pointer" onclick="window.location='{{ route('storefront.producto', $producto->id_producto) }}'">
                    <div class="absolute inset-0 bg-gradient-to-t from-gray-950/45 via-transparent to-transparent z-10 pointer-events-none"></div>
                    <span class="absolute top-4 left-4 z-20 px-3 py-1 rounded-md text-xs font-bold border {{ $isCafe ? 'bg-orange-50/95 text-coffee border-orange-100' : 'bg-white/95 text-gray-700 border-gray-200' }}">{{ $isCafe ? 'Cafe' : 'Minimarket' }}</span>

                    @if($stockWeb > 0)
                        <button @click="addToCart(@js($cartPayload))" class="absolute bottom-4 right-4 z-20 bg-white text-gray-900 hover:bg-ink hover:text-white rounded-lg p-3 shadow-lg transform translate-y-12 opacity-0 group-hover:translate-y-0 group-hover:opacity-100 transition-all duration-300 border border-white/80" title="Anadir">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                        </button>
                    @else
                        <span class="absolute bottom-4 right-4 z-20 bg-red-50 text-red-700 rounded-md px-3 py-2 text-xs font-bold border border-red-100">Agotado</span>
                    @endif

                    @if($presentacion->tiene_promocion)
                        <span class="absolute top-4 right-4 z-20 rounded-md bg-brand px-3 py-1 text-xs font-black text-white">Promo</span>
                    @endif
                </div>

                <div class="p-5 flex flex-col flex-1 bg-white relative z-20">
                    <button type="button" class="text-left" onclick="window.location='{{ route('storefront.producto', $producto->id_producto) }}'">
                        <p class="text-xs text-gray-500 font-semibold mb-1">{{ $categoryName }}</p>
                        <h3 class="font-bold text-gray-900 text-lg leading-tight mb-2 group-hover:text-brand transition-colors">{{ $producto->nombre_base }}</h3>
                    </button>

                    <p class="text-sm text-gray-500 mb-3 min-h-[40px]">{{ \Illuminate\Support\Str::limit($producto->descripcion ?: 'Producto disponible para pedido por WhatsApp.', 72) }}</p>

                    <div class="flex items-center justify-between text-xs text-gray-500 mb-4">
                        <span>{{ $presentacion->nombre_variante }}</span>
                        <span>{{ $stockWeb }} disp.</span>
                    </div>

                    <div class="mt-auto">
                        <div class="flex items-center justify-between mb-2">
                            <div>
                                @if($regularPrice)
                                    <p class="text-xs text-gray-400 font-medium line-through">S/ {{ number_format($regularPrice, 2) }}</p>
                                @endif
                                <p class="text-brand font-black text-xl">S/ {{ number_format($price, 2) }}</p>
                            </div>
                            <div class="text-right text-xs text-gray-500">
                                <p class="font-bold text-gray-700">{{ $stockWeb }} disp.</p>
                                <p>Stock web</p>
                            </div>
                        </div>
                        <p class="text-[11px] text-gray-400">IGV incluido: S/ {{ number_format($igvAmount, 2) }}</p>
                    </div>
                </div>
            </article>
        @empty
            <div class="col-span-full py-20 flex flex-col items-center justify-center bg-white rounded-lg border border-dashed border-gray-300">
                <div class="w-20 h-20 bg-gray-100 rounded-lg flex items-center justify-center mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">Vitrina sin productos</h3>
                <p class="text-gray-500 text-center max-w-sm">Crea productos de cafeteria o minimarket en el panel, o ejecuta los seeders para cargar la vitrina inicial.</p>
            </div>
        @endforelse
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endsection
