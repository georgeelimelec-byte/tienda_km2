@extends('storefront::layouts.master')

@section('content')
@php
    $selectedCategory = request('categoria_id');
    $activeFilter = request('filtro');
    $searchTerm = request('q');
    $heroSlides = ($bannersCarrusel ?? collect())->values();
    $primarySlide = $heroSlides->first();
    $secondarySlides = $heroSlides->slice(1, 3)->values();
    $promoBanners = ($bannersPromocionales ?? collect())->values();
    $lateralBanners = $promoBanners->where('posicion', 'Lateral')->values();
    $popupBanners = $promoBanners->where('posicion', 'Pop_up')->values();
    $hasFilteredView = $selectedCategory || $activeFilter || $searchTerm;
    $categoryCards = $categoryCards ?? collect();
    $destacados = $destacados ?? collect();
    $cafeProductos = $cafeProductos ?? collect();
    $marketProductos = $marketProductos ?? collect();
    $stockControlEnabled = $stockControlEnabled ?? \Modules\Storefront\Models\StorefrontSetting::current()->stockControlEnabled();
    $storefrontStats = $storefrontStats ?? [
        'productos' => $productos->count(),
        'presentaciones' => $productos->sum(fn ($producto) => $producto->presentaciones->count()),
        'promociones' => ($promociones ?? collect())->count(),
        'zonas' => 0,
    ];
    $heroImage = $primarySlide?->imagen_url ?: 'https://images.unsplash.com/photo-1604719312566-8912e9227c6a?auto=format&fit=crop&q=80&w=1800';
    $heroTitle = $primarySlide?->titulo ?: 'Market KM2';
    $heroLink = $primarySlide?->link_destino ?: route('storefront.index');
@endphp

<div class="-mx-4 overflow-x-hidden bg-[#f7f3ee] sm:-mx-6 lg:-mx-10">
    <section class="relative overflow-hidden bg-[#11100e]">
        <img src="{{ $heroImage }}" alt="{{ $heroTitle }}" class="absolute inset-0 h-full w-full object-cover opacity-54">
        <span class="absolute inset-0 bg-[linear-gradient(90deg,#11100e_0%,rgba(17,16,14,0.92)_32%,rgba(17,16,14,0.54)_68%,rgba(17,16,14,0.18)_100%)]"></span>
        <span class="absolute inset-x-0 bottom-0 h-32 bg-gradient-to-t from-[#f7f3ee] to-transparent"></span>

        <div class="relative mx-auto grid min-h-[620px] max-w-[1680px] grid-cols-1 items-end gap-8 overflow-hidden px-4 pb-16 pt-16 sm:px-6 lg:grid-cols-[minmax(0,1fr)_360px] lg:px-10">
            <div class="min-w-0 pb-6">
                <p class="mb-4 inline-flex rounded-md border border-orange-300/25 bg-orange-400/10 px-3 py-1.5 text-xs font-black uppercase tracking-wide text-orange-100">
                    {{ $heroTitle }}
                </p>
                <h1 class="max-w-5xl break-words text-4xl font-black leading-none text-white sm:text-6xl xl:text-7xl">
                    Tu market y cafe, listo para pedir.
                </h1>
                <p class="mt-6 max-w-2xl break-words text-base font-semibold leading-8 text-orange-50">
                    Explora productos, presentaciones y promociones desde una tienda moderna. El pedido queda registrado y el equipo lo confirma por el canal de atencion.
                </p>

                <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                    <a href="#catalogo" class="inline-flex h-12 w-full items-center justify-center gap-2 rounded-lg bg-brand px-5 text-sm font-black text-white shadow-lg shadow-orange-950/30 transition hover:bg-brand-dark sm:w-auto">
                        Ver productos
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </a>
                    <a href="{{ route('storefront.index', ['filtro' => 'promociones']) }}" class="inline-flex h-12 w-full items-center justify-center gap-2 rounded-lg border border-white/20 bg-white/10 px-5 text-sm font-black text-white backdrop-blur transition hover:bg-white/20 sm:w-auto">
                        Promociones
                    </a>
                </div>
            </div>

            <aside class="hidden min-w-0 pb-6 lg:block">
                <div class="border-l border-white/18 pl-6">
                    <p class="text-xs font-black uppercase tracking-wide text-orange-200">Vitrina activa</p>
                    <div class="mt-5 space-y-5">
                        <div>
                            <p class="text-4xl font-black text-white">{{ $storefrontStats['productos'] }}</p>
                            <p class="mt-1 text-sm font-bold text-orange-100">productos publicados</p>
                        </div>
                        <div>
                            <p class="text-4xl font-black text-white">{{ $storefrontStats['presentaciones'] }}</p>
                            <p class="mt-1 text-sm font-bold text-orange-100">{{ $stockControlEnabled ? 'presentaciones con precio y stock' : 'presentaciones en modo catalogo' }}</p>
                        </div>
                        @if($secondarySlides->isNotEmpty())
                            <div class="pt-2">
                                @foreach($secondarySlides->take(2) as $slide)
                                    <a href="{{ $slide->link_destino ?: route('storefront.index') }}" class="group mb-3 block border-t border-white/15 pt-3 text-sm font-black leading-snug text-white transition hover:text-orange-200">
                                        {{ $slide->titulo }}
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </aside>
        </div>
    </section>

    <div class="mx-auto max-w-[1680px] px-4 pb-20 sm:px-6 lg:px-10">
        <section class="mt-6 grid grid-cols-2 gap-3 md:grid-cols-4">
            <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
                <p class="text-xs font-black uppercase tracking-wide text-gray-400">Flujo</p>
                <p class="mt-1 text-sm font-black text-gray-950">Pedido web</p>
            </div>
            <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
                <p class="text-xs font-black uppercase tracking-wide text-gray-400">Modo stock</p>
                <p class="mt-1 text-sm font-black text-gray-950">{{ $stockControlEnabled ? 'Reserva al crear pedido' : 'Catalogo sin bloqueo' }}</p>
            </div>
            <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
                <p class="text-xs font-black uppercase tracking-wide text-gray-400">Cuenta</p>
                <p class="mt-1 text-sm font-black text-gray-950">Login unico</p>
            </div>
            <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
                <p class="text-xs font-black uppercase tracking-wide text-gray-400">Cobertura</p>
                <p class="mt-1 text-sm font-black text-gray-950">Recojo o delivery</p>
            </div>
        </section>

        @if($categoryCards->isNotEmpty())
            <section class="mt-12">
                <div class="mb-5 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="text-xs font-black uppercase tracking-wide text-brand">Categorias conectadas a la BD</p>
                        <h2 class="mt-1 text-3xl font-black text-gray-950">Lineas de compra</h2>
                    </div>
                    <a href="{{ route('storefront.index') }}" class="inline-flex w-fit items-center gap-2 rounded-lg bg-white px-4 py-3 text-sm font-black text-gray-800 shadow-sm ring-1 ring-gray-200 transition hover:text-brand">
                        Todas las categorias
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </a>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    @foreach($categoryCards->take(8) as $card)
                        <a href="{{ route('storefront.index', ['categoria_id' => $card['id']]) }}" class="group relative min-h-[220px] overflow-hidden rounded-lg border border-gray-200 bg-gray-950 shadow-sm">
                            @if($card['imagen'])
                                <img src="{{ $card['imagen'] }}" alt="{{ $card['nombre'] }}" class="absolute inset-0 h-full w-full object-cover opacity-66 transition duration-500 group-hover:scale-105">
                            @endif
                            <span class="absolute inset-0 bg-gradient-to-t from-gray-950 via-gray-950/40 to-transparent"></span>
                            <span class="absolute left-4 top-4 rounded-md bg-white/90 px-3 py-1 text-xs font-black uppercase tracking-wide text-gray-800">{{ $card['productos'] }} productos</span>
                            <div class="absolute inset-x-4 bottom-4">
                                <h3 class="text-2xl font-black text-white">{{ $card['nombre'] }}</h3>
                                <p class="mt-2 text-sm font-semibold text-orange-50">{{ $stockControlEnabled ? $card['stock'] . ' unidades disponibles' : 'Disponible para pedido' }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        <section class="mt-14">
            <div class="mb-5 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-xs font-black uppercase tracking-wide text-brand">Precios y descuentos vigentes</p>
                    <h2 class="mt-1 text-3xl font-black text-gray-950">Ofertas activas</h2>
                    <p class="mt-2 text-sm font-semibold text-gray-500">Promociones por producto o categoria, sin agregar tablas innecesarias.</p>
                </div>
                <a href="{{ route('storefront.index', ['filtro' => 'promociones']) }}" class="inline-flex w-fit items-center gap-2 rounded-lg bg-orange-50 px-4 py-3 text-sm font-black text-brand transition hover:bg-brand hover:text-white">
                    Ver promociones
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </a>
            </div>

            <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
                @foreach($lateralBanners as $banner)
                    <a href="{{ $banner->link_destino ?: route('storefront.index') }}" class="group relative min-h-[220px] overflow-hidden rounded-lg border border-gray-200 bg-gray-950 shadow-sm">
                        <img src="{{ $banner->imagen_url }}" alt="{{ $banner->titulo }}" class="absolute inset-0 h-full w-full object-cover transition duration-500 group-hover:scale-105">
                        <span class="absolute inset-0 bg-gradient-to-t from-gray-950/80 via-gray-950/30 to-transparent"></span>
                        <span class="absolute bottom-4 left-4 right-4 text-xl font-black text-white">{{ $banner->titulo }}</span>
                    </a>
                @endforeach

                @forelse(($promociones ?? collect())->take(6) as $promo)
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
                    <a href="{{ route('storefront.index', ['filtro' => 'promociones']) }}" class="group grid min-h-[220px] grid-cols-[42%_1fr] overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm transition hover:shadow-xl">
                        <div class="bg-gray-100">
                            @if($promoImage)
                                <img src="{{ $promoImage }}" alt="{{ $promo->nombre }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                            @endif
                        </div>
                        <div class="flex flex-col justify-center p-5">
                            <p class="mb-2 text-xs font-black uppercase tracking-wide text-brand">Promocion</p>
                            <h3 class="text-xl font-black leading-tight text-gray-950">{{ $promo->nombre }}</h3>
                            <p class="mt-3 text-sm font-semibold text-gray-500">{{ $promoScope ?: 'Productos seleccionados' }}</p>
                            <p class="mt-4 text-3xl font-black text-brand">-{{ $discountLabel }}</p>
                        </div>
                    </a>
                @empty
                    <div class="rounded-lg border border-dashed border-gray-300 bg-white p-8 lg:col-span-3">
                        <p class="text-lg font-black text-gray-950">No hay promociones activas.</p>
                        <p class="mt-2 text-sm font-semibold text-gray-500">Cuando se creen promociones desde el panel apareceran aqui.</p>
                    </div>
                @endforelse
            </div>
        </section>

        @if($destacados->isNotEmpty())
            <section class="mt-14">
                <div class="mb-5">
                    <p class="text-xs font-black uppercase tracking-wide text-brand">Presentaciones listas para pedir</p>
                    <h2 class="mt-1 text-3xl font-black text-gray-950">Destacados de la vitrina</h2>
                </div>
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach($destacados as $producto)
                        @include('storefront::partials.product-card', ['producto' => $producto, 'igv' => $igv, 'stockControlEnabled' => $stockControlEnabled])
                    @endforeach
                </div>
            </section>
        @endif

        <section class="mt-14 grid grid-cols-1 gap-6 xl:grid-cols-2">
            @if($cafeProductos->isNotEmpty())
                <div>
                    <div class="mb-5">
                        <p class="text-xs font-black uppercase tracking-wide text-brand">Cafe y panaderia</p>
                        <h2 class="mt-1 text-3xl font-black text-gray-950">Para recojo rapido</h2>
                    </div>
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        @foreach($cafeProductos->take(4) as $producto)
                            @include('storefront::partials.product-card', ['producto' => $producto, 'igv' => $igv, 'stockControlEnabled' => $stockControlEnabled])
                        @endforeach
                    </div>
                </div>
            @endif

            @if($marketProductos->isNotEmpty())
                <div>
                    <div class="mb-5">
                        <p class="text-xs font-black uppercase tracking-wide text-brand">Minimarket</p>
                        <h2 class="mt-1 text-3xl font-black text-gray-950">Basicos y antojos</h2>
                    </div>
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        @foreach($marketProductos->take(4) as $producto)
                            @include('storefront::partials.product-card', ['producto' => $producto, 'igv' => $igv, 'stockControlEnabled' => $stockControlEnabled])
                        @endforeach
                    </div>
                </div>
            @endif
        </section>

        <section id="catalogo" class="mt-16">
            <div class="mb-5 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="text-xs font-black uppercase tracking-wide text-brand">Catalogo operativo</p>
                    <h2 class="mt-1 text-3xl font-black text-gray-950">
                        @if($activeFilter === 'promociones')
                            Promociones KM2
                        @elseif($activeFilter === 'combos')
                            Combos KM2
                        @elseif($searchTerm)
                            Resultados para "{{ $searchTerm }}"
                        @else
                            Vitrina completa
                        @endif
                    </h2>
                    <p class="mt-2 text-sm font-semibold text-gray-500">{{ $productos->count() }} producto(s) disponibles con presentaciones{{ $stockControlEnabled ? ' y stock.' : ' en modo catalogo.' }}</p>
                </div>
                @if($hasFilteredView)
                    <a href="{{ route('storefront.index') }}" class="inline-flex w-fit items-center gap-2 rounded-lg bg-white px-4 py-3 text-sm font-black text-gray-800 shadow-sm ring-1 ring-gray-200 transition hover:text-brand">
                        Limpiar filtro
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 18 6M6 6l12 12"></path></svg>
                    </a>
                @endif
            </div>

            <div class="mb-6 flex gap-3 overflow-x-auto pb-2">
                <a href="{{ route('storefront.index', array_filter(['q' => request('q')])) }}" class="flex-none rounded-lg px-5 py-3 text-sm font-black shadow-sm ring-1 ring-gray-200 transition {{ !$selectedCategory ? 'bg-gray-950 text-white ring-gray-950' : 'bg-white text-gray-700 hover:text-brand' }}">
                    Todos
                </a>
                @foreach($categoriasTree as $root)
                    <a href="{{ route('storefront.index', array_filter(['categoria_id' => $root->id_categoria, 'q' => request('q')])) }}" class="flex-none rounded-lg px-5 py-3 text-sm font-black shadow-sm ring-1 ring-gray-200 transition {{ $selectedCategory == $root->id_categoria ? 'bg-brand text-white ring-brand' : 'bg-white text-gray-700 hover:text-brand' }}">
                        {{ $root->nombre }}
                    </a>
                    @foreach($root->hijos as $child)
                        <a href="{{ route('storefront.index', array_filter(['categoria_id' => $child->id_categoria, 'q' => request('q')])) }}" class="flex-none rounded-lg px-4 py-3 text-sm font-bold shadow-sm ring-1 ring-gray-200 transition {{ $selectedCategory == $child->id_categoria ? 'bg-brand text-white ring-brand' : 'bg-white text-gray-600 hover:text-brand' }}">
                            {{ $child->nombre }}
                        </a>
                    @endforeach
                @endforeach
            </div>

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 2xl:grid-cols-5">
                @forelse($productos as $producto)
                    @include('storefront::partials.product-card', ['producto' => $producto, 'igv' => $igv, 'stockControlEnabled' => $stockControlEnabled])
                @empty
                    <div class="col-span-full flex flex-col items-center justify-center rounded-lg border border-dashed border-gray-300 bg-white px-6 py-20 text-center">
                        <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-lg bg-gray-100">
                            <svg class="h-8 w-8 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                        </div>
                        <h3 class="text-xl font-black text-gray-950">Vitrina sin productos</h3>
                        <p class="mt-2 max-w-sm text-sm font-semibold text-gray-500">Crea productos y presentaciones activas en el panel para publicarlos en la tienda virtual.</p>
                    </div>
                @endforelse
            </div>
        </section>
    </div>
</div>

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
                <div class="p-5">
                    <p class="text-xs font-black uppercase tracking-wide text-brand">Promocion</p>
                    <h3 class="mt-1 text-xl font-black text-gray-950">{{ $popupBanner->titulo }}</h3>
                </div>
            </a>
        </div>
    </div>
@endif
@endsection
