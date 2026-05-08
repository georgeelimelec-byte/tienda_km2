<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    @php
        $storefrontSetting = \Modules\Storefront\Models\StorefrontSetting::current();
        $storeName = $storefrontSetting->store_name ?: 'Market KM2';
        $storeTagline = $storefrontSetting->store_tagline ?: 'Minimarket & Cafe';
        $storePrimaryColor = $storefrontSetting->primary_color ?: '#f97316';
        $storePrimaryLightColor = $storefrontSetting->primary_light_color ?: '#fb923c';
        $storePrimaryDarkColor = $storefrontSetting->primary_dark_color ?: '#ea580c';
        $storeAccentColor = $storefrontSetting->accent_color ?: '#1f2937';
        $storeHeaderStyle = $storefrontSetting->header_style ?: 'solid';
        $storeCardStyle = $storefrontSetting->card_style ?: 'rounded';
        $showLoginLink = (bool) $storefrontSetting->show_login_link;
        $storeFooterText = $storefrontSetting->footer_text ?: "{$storeName}. {$storeTagline}.";
        $storefrontLogoSrc = $storefrontSetting->displayLogoUrl();
    @endphp
    <title>{{ $storeTagline }} | {{ $storeName }}</title>
    <!-- Tailwind CDN instead of Vite due to local npm issues -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
      tailwind.config = {
        theme: {
          extend: {
            colors: {
              brand: {
                DEFAULT: @js($storePrimaryColor),
                light: @js($storePrimaryLightColor),
                dark: @js($storePrimaryDarkColor),
              },
              orange: {
                50: '#fff7ed',
                100: '#ffedd5',
                200: '#fed7aa',
                300: @js($storePrimaryLightColor),
                400: @js($storePrimaryLightColor),
                500: @js($storePrimaryColor),
                600: @js($storePrimaryColor),
                700: @js($storePrimaryDarkColor),
                800: @js($storePrimaryDarkColor),
                900: @js($storeAccentColor),
                950: @js($storeAccentColor),
              },
              coffee: {
                DEFAULT: '#7c2d12',
                light: '#b45309',
                dark: '#431407',
              },
              ink: {
                DEFAULT: @js($storeAccentColor),
                light: '#374151',
                dark: '#111827',
              }
            }
          }
        }
      }
    </script>
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        [x-cloak] { display: none !important; }
        :root {
            --store-primary: {{ $storePrimaryColor }};
            --store-primary-light: {{ $storePrimaryLightColor }};
            --store-primary-dark: {{ $storePrimaryDarkColor }};
            --store-accent: {{ $storeAccentColor }};
        }

        body.store-card-rounded main article.group { border-radius: 18px !important; }
        body.store-card-compact main article.group { border-radius: 10px !important; }
        body.store-card-flat main article.group { border-radius: 6px !important; box-shadow: none !important; }
    </style>
</head>
<body class="bg-white font-sans text-gray-800 antialiased store-header-{{ $storeHeaderStyle }} store-card-{{ $storeCardStyle }}" x-data="cartStore()">
    @php
        $headerSelectedCategory = request('categoria_id');
        $headerSearch = request('q');
        $headerActiveFilter = request('filtro');
        $headerCategoriasTree = isset($categoriasTree)
            ? $categoriasTree
            : \Modules\Inventory\Models\Categoria::whereNull('id_categoria_padre')
                ->where('estado', 'Activo')
                ->with(['hijos' => function ($q) {
                    $q->where('estado', 'Activo')
                        ->with(['hijos' => fn ($subQuery) => $subQuery->where('estado', 'Activo')->orderBy('nombre')])
                        ->orderBy('nombre');
                }])
                ->orderBy('nombre')
                ->get();
        $headerActiveRootId = optional($headerCategoriasTree->first())->id_categoria;

        if ($headerSelectedCategory) {
            foreach ($headerCategoriasTree as $root) {
                $rootMatches = (string) $root->id_categoria === (string) $headerSelectedCategory;
                $childMatches = $root->hijos->contains(function ($child) use ($headerSelectedCategory) {
                    return (string) $child->id_categoria === (string) $headerSelectedCategory
                        || $child->hijos->contains(fn ($grandChild) => (string) $grandChild->id_categoria === (string) $headerSelectedCategory);
                });

                if ($rootMatches || $childMatches) {
                    $headerActiveRootId = $root->id_categoria;
                    break;
                }
            }
        }
    @endphp
    
    <!-- Navbar -->
    <nav class="sticky top-0 z-50 border-b shadow-sm shadow-orange-950/15 transition-all duration-300" style="background: {{ $storeHeaderStyle === 'dark' ? $storeAccentColor : $storePrimaryColor }}; border-color: {{ $storeHeaderStyle === 'dark' ? $storeAccentColor : $storePrimaryDarkColor }};" x-data="{ mobileMenuOpen: false, categoriesOpen: false, activeRoot: @js((string) $headerActiveRootId) }" @keydown.escape.window="mobileMenuOpen = false; categoriesOpen = false">
        <div class="w-full px-4 sm:px-6 lg:px-10">
            <div class="mx-auto flex min-h-[74px] max-w-[1800px] items-center gap-3 xl:gap-4">
                <!-- Logo -->
                <div class="flex flex-shrink-0 items-center group cursor-pointer" onclick="window.location='{{ route('storefront.index') }}'">
                    @if($storefrontLogoSrc)
                        <span class="inline-flex h-16 w-20 items-center justify-center transition-transform duration-300 group-hover:scale-105">
                            <img src="{{ $storefrontLogoSrc }}" alt="{{ $storeName }}" class="h-full w-full object-contain">
                        </span>
                    @else
                        <div class="w-11 h-11 bg-white rounded-lg flex items-center justify-center text-brand transition-transform duration-300 group-hover:scale-105">
                            <span class="font-extrabold text-xl font-serif">K</span>
                        </div>
                        <div class="ml-3 leading-tight text-white">
                            <span class="block font-extrabold text-xl">{{ $storeName }}</span>
                            <span class="hidden sm:block text-xs font-bold uppercase text-orange-100">{{ $storeTagline }}</span>
                        </div>
                    @endif
                </div>

                <!-- Desktop Navigation -->
                <div class="hidden items-center gap-2 text-sm font-bold text-white xl:flex">
                    <div @click.outside="categoriesOpen = false">
                        <button type="button" @click="categoriesOpen = !categoriesOpen" class="inline-flex h-11 items-center gap-2 rounded-lg bg-orange-800 px-4 text-white shadow-sm shadow-orange-950/20 transition hover:bg-orange-900 focus:outline-none focus:ring-2 focus:ring-white/60" :aria-expanded="categoriesOpen.toString()">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18"></path></svg>
                            Categorias
                            <svg class="h-4 w-4 transition-transform" :class="categoriesOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>

                        <div x-cloak x-show="categoriesOpen" x-transition.opacity class="fixed inset-x-0 top-[74px] z-50 bg-gray-950/30 px-4 pb-6 pt-3 backdrop-blur-[2px]" @click.self="categoriesOpen = false">
                            <div class="mx-auto grid max-h-[calc(100vh-104px)] max-w-[1500px] grid-cols-[300px_minmax(0,1fr)] overflow-hidden rounded-lg bg-white text-gray-900 shadow-2xl ring-1 ring-orange-950/10">
                                <aside class="overflow-y-auto border-r border-orange-100 bg-gradient-to-b from-orange-50 to-white py-4">
                                    <div class="px-4 pb-3">
                                        <p class="text-xs font-black uppercase tracking-wide text-orange-500">Explorar tienda</p>
                                        <a href="{{ route('storefront.index', array_filter(['q' => $headerSearch])) }}" class="mt-2 flex items-center justify-between rounded-lg border border-orange-100 bg-white px-4 py-3 text-sm font-extrabold text-gray-800 shadow-sm transition hover:border-brand hover:text-brand">
                                            Todas las categorias
                                            <svg class="h-4 w-4 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                        </a>
                                    </div>

                                    @foreach($headerCategoriasTree as $root)
                                        <button type="button" @mouseenter="activeRoot = @js((string) $root->id_categoria)" @focus="activeRoot = @js((string) $root->id_categoria)" @click="activeRoot = @js((string) $root->id_categoria)" class="mx-4 mb-1 flex w-[calc(100%-2rem)] items-center justify-between rounded-lg border px-3 py-3 text-left text-sm font-bold transition" :class="activeRoot === @js((string) $root->id_categoria) ? 'border-brand bg-brand text-white shadow-md shadow-orange-900/15' : 'border-transparent bg-white/60 text-gray-700 hover:border-orange-100 hover:bg-white hover:text-brand'">
                                            <span class="flex min-w-0 items-center gap-3">
                                                <span class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-md" :class="activeRoot === @js((string) $root->id_categoria) ? 'bg-white/20 text-white' : 'bg-orange-100 text-brand'">
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7h16M4 12h16M4 17h16"></path></svg>
                                                </span>
                                                <span class="min-w-0">
                                                    <span class="block truncate">{{ $root->nombre }}</span>
                                                    <span class="block text-xs font-semibold" :class="activeRoot === @js((string) $root->id_categoria) ? 'text-orange-100' : 'text-gray-400'">{{ $root->hijos->count() }} seccion(es)</span>
                                                </span>
                                            </span>
                                            <svg class="h-4 w-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                        </button>
                                    @endforeach
                                </aside>

                                <section class="overflow-y-auto bg-white p-6">
                                    @foreach($headerCategoriasTree as $root)
                                        <div x-show="activeRoot === @js((string) $root->id_categoria)" x-cloak>
                                            <div class="mb-6 flex items-start justify-between gap-4 rounded-lg border border-orange-100 bg-gradient-to-r from-orange-50 via-white to-white p-5">
                                                <div>
                                                    <div class="mb-2 flex items-center gap-3">
                                                        <span class="flex h-11 w-11 items-center justify-center rounded-lg bg-brand text-white shadow-md shadow-orange-900/20">
                                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18"></path></svg>
                                                        </span>
                                                        <div>
                                                            <h2 class="text-2xl font-black text-gray-900">{{ $root->nombre }}</h2>
                                                            <p class="text-sm font-semibold text-gray-500">{{ $root->hijos->count() }} subcategoria(s) disponible(s)</p>
                                                        </div>
                                                    </div>
                                                    <a href="{{ route('storefront.index', array_filter(['categoria_id' => $root->id_categoria, 'q' => $headerSearch])) }}" class="ml-[56px] inline-flex rounded-md bg-brand px-3 py-1.5 text-sm font-bold text-white transition hover:bg-brand-dark">Ver todo</a>
                                                </div>
                                                <button type="button" @click="categoriesOpen = false" class="rounded-lg p-2 text-gray-400 transition hover:bg-white hover:text-gray-700" aria-label="Cerrar categorias">
                                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                </button>
                                            </div>

                                            @if($root->hijos->isNotEmpty())
                                                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4">
                                                    @foreach($root->hijos as $child)
                                                        <div class="group rounded-lg border border-gray-200 bg-white p-4 shadow-sm transition hover:-translate-y-0.5 hover:border-orange-200 hover:shadow-lg hover:shadow-orange-950/5">
                                                            <div class="flex items-start justify-between gap-3 border-l-2 border-brand pl-3">
                                                                <div class="min-w-0">
                                                                    <a href="{{ route('storefront.index', array_filter(['categoria_id' => $child->id_categoria, 'q' => $headerSearch])) }}" class="block text-sm font-black uppercase tracking-wide text-gray-950 transition group-hover:text-brand">{{ $child->nombre }}</a>
                                                                    <p class="mt-1 text-xs font-semibold text-gray-400">{{ $child->hijos->count() }} opcion(es)</p>
                                                                </div>
                                                                <a href="{{ route('storefront.index', array_filter(['categoria_id' => $child->id_categoria, 'q' => $headerSearch])) }}" class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-md bg-orange-50 text-brand transition group-hover:bg-brand group-hover:text-white" aria-label="Ver {{ $child->nombre }}">
                                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                                                </a>
                                                            </div>

                                                            @if($child->hijos->isNotEmpty())
                                                                <div class="mt-4 space-y-2 pl-3">
                                                                    @foreach($child->hijos as $grandChild)
                                                                        <a href="{{ route('storefront.index', array_filter(['categoria_id' => $grandChild->id_categoria, 'q' => $headerSearch])) }}" class="flex items-center gap-2 rounded-md px-2 py-1.5 text-sm font-medium text-slate-600 transition hover:bg-orange-50 hover:text-brand">
                                                                            <span class="h-1.5 w-1.5 rounded-full bg-orange-300"></span>
                                                                            <span class="min-w-0 truncate">{{ $grandChild->nombre }}</span>
                                                                        </a>
                                                                    @endforeach
                                                                </div>
                                                            @else
                                                                <a href="{{ route('storefront.index', array_filter(['categoria_id' => $child->id_categoria, 'q' => $headerSearch])) }}" class="mt-4 inline-flex rounded-md bg-orange-50 px-3 py-2 text-sm font-bold text-brand transition hover:bg-brand hover:text-white">Ver productos</a>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <div class="rounded-lg border border-dashed border-orange-200 bg-orange-50 p-6">
                                                    <p class="text-sm font-semibold text-gray-700">Esta categoria no tiene subcategorias configuradas.</p>
                                                    <a href="{{ route('storefront.index', array_filter(['categoria_id' => $root->id_categoria, 'q' => $headerSearch])) }}" class="mt-3 inline-flex rounded-lg bg-brand px-4 py-2 text-sm font-bold text-white transition hover:bg-brand-dark">Ver productos</a>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </section>
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('storefront.index', ['filtro' => 'promociones']) }}" class="inline-flex h-11 items-center gap-2 rounded-lg px-3.5 transition focus:outline-none focus:ring-2 focus:ring-white/60 {{ $headerActiveFilter === 'promociones' ? 'bg-white text-brand shadow-sm' : 'bg-white/10 text-white hover:bg-white/20' }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a4 4 0 118 0v2M12 8V6a4 4 0 00-8 0v2m8 0H4m8 0h8"></path></svg>
                        Promociones
                    </a>

                    <a href="{{ route('storefront.index', ['filtro' => 'combos']) }}" class="inline-flex h-11 items-center gap-2 rounded-lg px-3.5 transition focus:outline-none focus:ring-2 focus:ring-white/60 {{ $headerActiveFilter === 'combos' ? 'bg-white text-brand shadow-sm' : 'bg-white/10 text-white hover:bg-white/20' }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12v7a2 2 0 01-2 2H6a2 2 0 01-2-2v-7m16 0H4m16 0l-2-5H6l-2 5m8-5v14"></path></svg>
                        Combos
                    </a>
                </div>

                <!-- Search -->
                <form action="{{ route('storefront.index') }}" method="GET" class="hidden h-12 min-w-[280px] flex-1 items-center rounded-lg bg-white p-1 shadow-sm ring-1 ring-orange-950/10 transition focus-within:ring-2 focus-within:ring-orange-200 md:flex">
                    @if($headerSelectedCategory)
                        <input type="hidden" name="categoria_id" value="{{ $headerSelectedCategory }}">
                    @endif
                    @if($headerActiveFilter)
                        <input type="hidden" name="filtro" value="{{ $headerActiveFilter }}">
                    @endif
                    <svg class="ml-3 h-5 w-5 flex-shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    <input type="search" name="q" value="{{ $headerSearch }}" placeholder="Buscar productos..." class="min-w-0 flex-1 bg-transparent px-3 py-2 text-sm font-semibold text-gray-900 placeholder-gray-400 outline-none">
                    @if($headerSearch)
                        <a href="{{ route('storefront.index', array_filter(['categoria_id' => $headerSelectedCategory])) }}" class="rounded-md p-2 text-gray-400 transition hover:bg-gray-100 hover:text-gray-700" aria-label="Limpiar busqueda">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </a>
                    @endif
                    <button type="submit" class="inline-flex h-10 items-center justify-center rounded-md bg-ink px-5 text-sm font-bold text-white transition hover:bg-gray-900">
                        Buscar
                    </button>
                </form>

                <!-- Navbar Actions -->
                <div class="ml-auto flex flex-shrink-0 items-center gap-2 sm:gap-3">
                    <!-- Admin Login Button -->
                    @if($showLoginLink)
                        @auth
                            <a href="{{ route('admin.dashboard.main') }}" class="hidden h-11 items-center gap-2 rounded-lg bg-white px-4 text-sm font-bold text-gray-900 shadow-sm transition hover:bg-orange-50 sm:flex">
                                <span>Panel Admin</span>
                            </a>
                        @else
                            <a href="{{ route('auth.login') }}" class="hidden h-11 items-center gap-2 rounded-lg bg-white px-4 text-sm font-bold text-gray-900 shadow-sm transition hover:bg-orange-50 sm:flex">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                                <span>Ingresar</span>
                            </a>
                        @endauth
                    @endif

                    <!-- Cart Toggle -->
                    <button @click="cartOpen = true" class="relative inline-flex h-11 w-11 items-center justify-center rounded-lg bg-orange-800 text-white shadow-sm transition hover:bg-orange-900 focus:outline-none focus:ring-2 focus:ring-white/60">
                        <span class="absolute top-0 right-0 -mt-1 -mr-1 flex h-5 w-5 items-center justify-center rounded-full bg-ink text-[10px] font-bold text-white shadow-sm border-2 border-brand transition-transform" x-show="totalItems > 0" x-text="totalItems" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="scale-0" x-transition:enter-end="scale-100"></span>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </button>
                    
                    <!-- Mobile Menu Toggle -->
                    <button type="button" @click="mobileMenuOpen = !mobileMenuOpen" class="inline-flex h-11 w-11 items-center justify-center rounded-lg bg-orange-800 text-white shadow-sm transition hover:bg-orange-900 focus:outline-none focus:ring-2 focus:ring-white/60 xl:hidden" aria-label="Abrir menu" :aria-expanded="mobileMenuOpen.toString()">
                        <svg x-show="!mobileMenuOpen" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                        <svg x-cloak x-show="mobileMenuOpen" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
            </div>

            <form action="{{ route('storefront.index') }}" method="GET" class="mx-auto mb-3 flex items-center rounded-lg bg-white p-1 shadow-sm ring-1 ring-orange-950/10 md:hidden">
                @if($headerSelectedCategory)
                    <input type="hidden" name="categoria_id" value="{{ $headerSelectedCategory }}">
                @endif
                @if($headerActiveFilter)
                    <input type="hidden" name="filtro" value="{{ $headerActiveFilter }}">
                @endif
                <svg class="ml-3 h-5 w-5 flex-shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                <input type="search" name="q" value="{{ $headerSearch }}" placeholder="Buscar productos..." class="min-w-0 flex-1 bg-transparent px-3 py-2 text-sm font-medium text-gray-900 placeholder-gray-400 outline-none">
                <button type="submit" class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-ink text-white transition hover:bg-gray-900" aria-label="Buscar">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14m-6-6l6 6-6 6"></path></svg>
                </button>
            </form>

            <div x-cloak x-show="mobileMenuOpen" x-transition class="pb-4 xl:hidden">
                <div class="rounded-lg bg-white p-2 shadow-lg ring-1 ring-orange-950/10">
                    <div>
                        <p class="px-3 pb-1 text-xs font-black uppercase text-gray-400">Categorias</p>
                        <a href="{{ route('storefront.index', array_filter(['q' => $headerSearch])) }}" class="flex items-center rounded-lg px-3 py-2.5 text-sm font-bold transition {{ !$headerSelectedCategory ? 'bg-orange-50 text-brand' : 'text-gray-700 hover:bg-gray-50 hover:text-brand' }}">
                            Todas las categorias
                        </a>
                        @foreach($headerCategoriasTree as $root)
                            <a href="{{ route('storefront.index', array_filter(['categoria_id' => $root->id_categoria, 'q' => $headerSearch])) }}" class="flex items-center rounded-lg px-3 py-2.5 text-sm font-bold transition {{ $headerSelectedCategory == $root->id_categoria ? 'bg-brand text-white' : 'text-gray-700 hover:bg-orange-50 hover:text-brand' }}">
                                {{ $root->nombre }}
                            </a>
                            @foreach($root->hijos as $child)
                                <a href="{{ route('storefront.index', array_filter(['categoria_id' => $child->id_categoria, 'q' => $headerSearch])) }}" class="ml-3 flex items-center rounded-lg px-3 py-2 text-sm font-semibold transition {{ $headerSelectedCategory == $child->id_categoria ? 'bg-orange-100 text-brand' : 'text-gray-600 hover:bg-gray-50 hover:text-brand' }}">
                                    {{ $child->nombre }}
                                </a>
                            @endforeach
                        @endforeach
                    </div>
                    <div class="mt-2 border-t border-gray-100 pt-2">
                        <p class="px-3 pb-1 text-xs font-black uppercase text-gray-400">Especiales</p>
                        <a href="{{ route('storefront.index', ['filtro' => 'promociones']) }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-bold transition {{ $headerActiveFilter === 'promociones' ? 'bg-orange-50 text-brand' : 'text-gray-700 hover:bg-gray-50 hover:text-brand' }}">
                            <svg class="h-5 w-5 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a4 4 0 118 0v2M12 8V6a4 4 0 00-8 0v2m8 0H4m8 0h8"></path></svg>
                            Promociones
                        </a>
                        <a href="{{ route('storefront.index', ['filtro' => 'combos']) }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-bold transition {{ $headerActiveFilter === 'combos' ? 'bg-orange-50 text-brand' : 'text-gray-700 hover:bg-gray-50 hover:text-brand' }}">
                            <svg class="h-5 w-5 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12v7a2 2 0 01-2 2H6a2 2 0 01-2-2v-7m16 0H4m16 0l-2-5H6l-2 5m8-5v14"></path></svg>
                            Combos
                        </a>
                    </div>
                    @if($showLoginLink)
                        <div class="mt-2 border-t border-gray-100 pt-2 sm:hidden">
                            <a href="{{ auth()->check() ? route('admin.dashboard.main') : route('auth.login') }}" class="flex items-center gap-3 rounded-lg px-3 py-3 text-sm font-bold text-gray-700 transition hover:bg-gray-50 hover:text-brand">
                                <svg class="h-5 w-5 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                {{ auth()->check() ? 'Panel Admin' : 'Ingresar' }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="w-full pb-24 px-4 sm:px-6 lg:px-10 relative min-h-screen">
        <div class="absolute inset-0 bg-gray-50 -z-10"></div>
        @yield('content')
    </main>

    <!-- Cart Sidebar Modal -->
    <div x-show="cartOpen" style="display: none;" class="fixed inset-0 z-50 flex justify-end">
        <div x-show="cartOpen" @click="cartOpen = false" x-transition.opacity class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm"></div>
        
        <div x-show="cartOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full" class="relative w-full max-w-md bg-white h-full shadow-2xl flex flex-col pointer-events-auto">
            <div class="px-6 py-5 flex justify-between items-center border-b border-gray-100">
                <h2 class="text-xl font-extrabold text-gray-900 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-brand" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
                    Tu Pedido
                </h2>
                <button @click="cartOpen = false" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></button>
            </div>
            
            <!-- Items -->
            <div class="flex-1 overflow-y-auto p-6 flex flex-col gap-4 bg-gray-50/50">
                <template x-if="items.length === 0">
                    <div class="flex flex-col items-center justify-center h-full text-gray-400 space-y-4">
                        <div class="w-24 h-24 bg-gray-100 rounded-lg flex items-center justify-center mb-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <p class="font-medium">Tu carrito esta vacio</p>
                        <button @click="cartOpen = false" class="text-brand font-semibold text-sm hover:underline">Explorar catalogo</button>
                    </div>
                </template>
                <template x-for="(item, index) in items" :key="index">
                    <div class="flex gap-4 bg-white p-3 rounded-lg shadow-sm border border-gray-100 items-center transform transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
                        <img :src="item.image" class="w-20 h-20 rounded-md object-cover shadow-sm" alt="product">
                        <div class="flex-1 flex flex-col justify-center">
                            <p class="font-bold text-gray-800 leading-tight mb-1" x-text="item.name"></p>
                            <p class="text-xs text-gray-400" x-show="item.variant" x-text="item.variant"></p>
                            <span class="font-extrabold text-brand text-sm" x-text="'S/ ' + Number(item.price).toFixed(2)"></span>
                            <div class="flex justify-start items-center mt-2">
                                <div class="flex items-center bg-gray-50 border border-gray-200 rounded-lg p-1">
                                    <button @click="updateQuantity(index, -1)" class="w-7 h-7 flex items-center justify-center text-gray-600 hover:bg-gray-200 rounded-md transition-colors leading-none">&minus;</button>
                                    <span class="text-sm font-bold w-6 text-center text-gray-800" x-text="item.quantity"></span>
                                    <button @click="updateQuantity(index, 1)" class="w-7 h-7 flex items-center justify-center text-gray-600 hover:bg-gray-200 rounded-md transition-colors leading-none">&plus;</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Footer -->
            <div x-show="items.length > 0" class="border-t border-gray-100 p-6 bg-white shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)]">
                <div class="flex justify-between items-center mb-4">
                    <span class="text-gray-500 font-medium text-sm">Subtotal Estimado</span>
                    <span class="text-2xl font-black text-gray-900" x-text="'S/ ' + Number(subtotal).toFixed(2)"></span>
                </div>
                <!-- RF01: Flujo a Checkout -->
                <a href="{{ route('storefront.checkout') }}" class="w-full relative group overflow-hidden rounded-lg flex items-center justify-center py-4 bg-ink text-white font-bold text-lg shadow-lg shadow-gray-900/20 transition-all hover:bg-brand hover:-translate-y-0.5">
                    <span class="absolute right-0 w-8 h-32 -mt-12 transition-all duration-1000 transform translate-x-12 bg-white opacity-10 rotate-12 group-hover:-translate-x-96 ease"></span>
                    Enviar pedido
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                </a>
            </div>
        </div>
    </div>

    <!-- Footer Simple -->
    <footer class="border-t border-gray-100 bg-white/50 backdrop-blur-md py-8 mt-12 relative z-10 text-center">
        <p class="text-gray-500 font-medium text-sm">© {{ date('Y') }} {{ $storeFooterText }}</p>
        @if($showLoginLink)
        <div class="mt-4">
            <a href="{{ route('auth.login') }}" class="text-gray-300 hover:text-brand transition-colors text-xs inline-flex items-center gap-1 opacity-50 hover:opacity-100">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                Acceso Personal
            </a>
        </div>
        @endif
    </footer>
    
    <!-- Alpine Scripts (Store logic) -->

    <!-- Alpine Data Store -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('cartStore', () => ({
                cartOpen: false,
                items: JSON.parse(localStorage.getItem('km2_cart') || '[]'),
                get totalItems() {
                    return this.items.reduce((acc, item) => acc + item.quantity, 0);
                },
                get subtotal() {
                    return this.items.reduce((acc, item) => acc + (item.price * item.quantity), 0);
                },
                addToCart(product) {
                    product.id = product.presentation_id || product.id;
                    product.quantity = product.quantity ? parseInt(product.quantity) : 1;

                    const existing = this.items.find(i => String(i.id) === String(product.id));
                    const qtyToAdd = product.quantity ? parseInt(product.quantity) : 1;
                    const maxStock = Number(product.max_stock || product.stock || 0);
                    
                    if (existing) {
                        existing.quantity += qtyToAdd;
                        if (maxStock > 0 && existing.quantity > maxStock) {
                            existing.quantity = maxStock;
                        }
                    } else {
                        this.items.push({...product, quantity: maxStock > 0 ? Math.min(qtyToAdd, maxStock) : qtyToAdd});
                    }
                    this.save();
                    this.cartOpen = true;
                },
                updateQuantity(index, change) {
                    this.items[index].quantity += change;
                    const maxStock = Number(this.items[index].max_stock || this.items[index].stock || 0);
                    if (maxStock > 0 && this.items[index].quantity > maxStock) {
                        this.items[index].quantity = maxStock;
                    }
                    if (this.items[index].quantity <= 0) {
                        this.items.splice(index, 1);
                    }
                    this.save();
                },
                save() {
                    localStorage.setItem('km2_cart', JSON.stringify(this.items));
                }
            }))
        })
    </script>
</body>
</html>
