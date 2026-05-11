@php
    $presentacion = $producto->presentaciones->first();
    $stockControlEnabled = $stockControlEnabled ?? \Modules\Storefront\Models\StorefrontSetting::current()->stockControlEnabled();
@endphp

@if($presentacion)
    @php
        $img = optional($presentacion->imagenes->first())->url ?: $producto->imagen_principal_url;
        $price = (float) $presentacion->precio_efectivo;
        $basePrice = (float) $presentacion->precio;
        $referencePrice = $presentacion->precio_referencial !== null ? (float) $presentacion->precio_referencial : null;
        $regularPrice = $referencePrice && $referencePrice > $price
            ? $referencePrice
            : (($presentacion->tiene_promocion && $basePrice > $price) ? $basePrice : null);
        $stock = (int) $presentacion->stock;
        $igvAmount = ($igv ?? 0) > 0 ? $price * ((float) $igv / (100 + (float) $igv)) : 0;
        $category = $producto->categoria;
        $categoryName = $category->nombre ?? 'Sin categoria';
        $parentName = $category && $category->padre ? $category->padre->nombre : $categoryName;
        $categoryContext = strtolower($parentName . ' ' . $categoryName . ' ' . $producto->nombre_base);
        $isCafe = str_contains($categoryContext, 'cafe')
            || str_contains($categoryContext, 'cafeteria')
            || str_contains($categoryContext, 'panaderia')
            || str_contains($categoryContext, 'sandwich')
            || str_contains($categoryContext, 'postre');
        $variants = $producto->presentaciones->take(3);
        $cartPayload = [
            'id' => $presentacion->id_presentacion,
            'presentation_id' => $presentacion->id_presentacion,
            'product_id' => $producto->id_producto,
            'name' => $producto->nombre_base,
            'variant' => $presentacion->nombre_variante,
            'price' => $price,
            'image' => $img,
            'stock' => $stock,
            'max_stock' => $stockControlEnabled ? $stock : null,
            'stock_control_enabled' => $stockControlEnabled,
        ];
    @endphp

    <article class="group flex h-full flex-col overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm transition duration-300 hover:-translate-y-1 hover:border-orange-200 hover:shadow-xl">
        <a href="{{ route('storefront.producto', $producto->id_producto) }}" class="relative block aspect-[4/3] overflow-hidden bg-[#f3eee8]">
            <img src="{{ $img }}" alt="{{ $producto->nombre_base }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
            <span class="absolute inset-x-0 bottom-0 h-24 bg-gradient-to-t from-gray-950/60 to-transparent"></span>

            <span class="absolute left-3 top-3 rounded-md border border-white/70 bg-white/90 px-2.5 py-1 text-[11px] font-black uppercase tracking-wide {{ $isCafe ? 'text-orange-800' : 'text-gray-700' }}">
                {{ $isCafe ? 'Cafe' : 'Market' }}
            </span>

            @if($presentacion->tiene_promocion)
                <span class="absolute right-3 top-3 rounded-md bg-brand px-2.5 py-1 text-[11px] font-black uppercase tracking-wide text-white shadow">
                    Promo
                </span>
            @endif

            @if($stockControlEnabled)
                <span class="absolute bottom-3 left-3 rounded-md bg-gray-950/70 px-2.5 py-1 text-[11px] font-bold text-white backdrop-blur">
                    {{ $stock }} disponibles
                </span>
            @else
                <span class="absolute bottom-3 left-3 rounded-md bg-gray-950/70 px-2.5 py-1 text-[11px] font-bold text-white backdrop-blur">
                    Disponible para pedido
                </span>
            @endif
        </a>

        <div class="flex flex-1 flex-col p-4">
            <div class="mb-3">
                <p class="mb-1 text-xs font-bold uppercase tracking-wide text-gray-500">{{ $categoryName }}</p>
                <a href="{{ route('storefront.producto', $producto->id_producto) }}" class="block">
                    <h3 class="text-base font-black leading-snug text-gray-950 transition group-hover:text-brand">{{ $producto->nombre_base }}</h3>
                </a>
                <p class="mt-2 min-h-[40px] text-sm leading-relaxed text-gray-500">
                    {{ \Illuminate\Support\Str::limit($producto->descripcion ?: 'Disponible para pedido por WhatsApp.', 72) }}
                </p>
            </div>

            <div class="mb-4 flex flex-wrap gap-2">
                @foreach($variants as $variant)
                    <span class="rounded-md border border-gray-200 bg-gray-50 px-2 py-1 text-[11px] font-bold text-gray-600">
                        {{ $variant->nombre_variante }}@if($stockControlEnabled) - {{ (int) $variant->stock }}@endif
                    </span>
                @endforeach
            </div>

            <div class="mt-auto border-t border-gray-100 pt-4">
                <div class="mb-3 flex items-end justify-between gap-3">
                    <div>
                        @if($regularPrice)
                            <p class="text-xs font-semibold text-gray-400 line-through">S/ {{ number_format($regularPrice, 2) }}</p>
                        @endif
                        <p class="text-2xl font-black text-gray-950">S/ {{ number_format($price, 2) }}</p>
                        <p class="text-[11px] font-semibold text-gray-400">IGV incluido: S/ {{ number_format($igvAmount, 2) }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs font-black uppercase tracking-wide text-gray-400">Presentacion</p>
                        <p class="text-sm font-bold text-gray-800">{{ $presentacion->nombre_variante }}</p>
                    </div>
                </div>

                @if(! $stockControlEnabled || $stock > 0)
                    <button
                        type="button"
                        @click="addToCart(@js($cartPayload))"
                        class="flex h-11 w-full items-center justify-center gap-2 rounded-lg bg-gray-950 px-4 text-sm font-black text-white transition hover:bg-brand focus:outline-none focus:ring-2 focus:ring-orange-200"
                    >
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Agregar al pedido
                    </button>
                @else
                    <button type="button" disabled class="flex h-11 w-full items-center justify-center rounded-lg bg-gray-100 px-4 text-sm font-black text-gray-400">
                        Sin stock
                    </button>
                @endif
            </div>
        </div>
    </article>
@endif
