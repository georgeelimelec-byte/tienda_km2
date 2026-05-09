@extends('layouts.admin')

@section('title', 'Promociones')
@section('page-title', 'Promociones')
@section('page-kicker', 'Reglas comerciales de tienda virtual')

@section('topbar-actions')
    <span class="topbar-badge"><i class="fas fa-percent"></i> Productos o categorias</span>
@endsection

@section('content')
    <div class="mx-auto w-full max-w-[1400px] space-y-6">
        @if($errors->any())
            <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm font-semibold text-red-700">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <section class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
            <div class="mb-4">
                <h2 class="text-xl font-black text-gray-900">Nueva promocion</h2>
                <p class="text-sm text-gray-500">Aplica descuentos por producto o por categoria completa sin tocar el precio base del producto.</p>
            </div>

            <form method="POST" action="{{ route('admin.promociones.store') }}" class="grid gap-4 lg:grid-cols-4">
                @csrf
                <div class="lg:col-span-2">
                    <label class="mb-1 block text-sm font-bold text-gray-700">Nombre</label>
                    <input name="nombre" class="w-full rounded-lg border-gray-200 bg-gray-50 px-4 py-2.5 focus:border-brand focus:ring-brand" required placeholder="Ej. Semana cafeteria KM2">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-bold text-gray-700">Tipo</label>
                    <select name="tipo_descuento" class="w-full rounded-lg border-gray-200 bg-gray-50 px-4 py-2.5 focus:border-brand focus:ring-brand">
                        <option value="Porcentaje">Porcentaje</option>
                        <option value="Monto">Monto fijo</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-bold text-gray-700">Valor</label>
                    <input type="number" step="0.01" min="0.01" name="valor_descuento" class="w-full rounded-lg border-gray-200 bg-gray-50 px-4 py-2.5 focus:border-brand focus:ring-brand" required>
                </div>
                <div class="lg:col-span-2">
                    <label class="mb-1 block text-sm font-bold text-gray-700">Productos</label>
                    <select name="producto_ids[]" multiple class="h-36 w-full rounded-lg border-gray-200 bg-gray-50 px-4 py-2.5 focus:border-brand focus:ring-brand">
                        @foreach($productos as $producto)
                            <option value="{{ $producto->id_producto }}">{{ $producto->nombre_base }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="lg:col-span-2">
                    <label class="mb-1 block text-sm font-bold text-gray-700">Categorias</label>
                    <select name="categoria_ids[]" multiple class="h-36 w-full rounded-lg border-gray-200 bg-gray-50 px-4 py-2.5 focus:border-brand focus:ring-brand">
                        @foreach($categorias as $categoria)
                            <option value="{{ $categoria->id_categoria }}">{{ $categoria->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-bold text-gray-700">Inicio</label>
                    <input type="date" name="fecha_inicio" class="w-full rounded-lg border-gray-200 bg-gray-50 px-4 py-2.5 focus:border-brand focus:ring-brand">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-bold text-gray-700">Fin</label>
                    <input type="date" name="fecha_fin" class="w-full rounded-lg border-gray-200 bg-gray-50 px-4 py-2.5 focus:border-brand focus:ring-brand">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-bold text-gray-700">Estado</label>
                    <select name="estado" class="w-full rounded-lg border-gray-200 bg-gray-50 px-4 py-2.5 focus:border-brand focus:ring-brand">
                        <option value="Activo">Activo</option>
                        <option value="Inactivo">Inactivo</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button class="w-full rounded-lg bg-brand px-4 py-2.5 font-bold text-white transition hover:bg-brand-dark">Crear promocion</button>
                </div>
                <div class="lg:col-span-4">
                    <label class="mb-1 block text-sm font-bold text-gray-700">Descripcion</label>
                    <textarea name="descripcion" rows="2" class="w-full rounded-lg border-gray-200 bg-gray-50 px-4 py-2.5 focus:border-brand focus:ring-brand" placeholder="Detalle interno o texto de campana"></textarea>
                </div>
            </form>
        </section>

        <section class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-100 px-5 py-4">
                <h2 class="text-xl font-black text-gray-900">Promociones registradas</h2>
            </div>

            <div class="divide-y divide-gray-100">
                @forelse($promociones as $promocion)
                    @php
                        $selectedProducts = $promocion->productos->pluck('id_producto')->all();
                        $selectedCategories = $promocion->categorias->pluck('id_categoria')->all();
                    @endphp
                    <details class="group">
                        <summary class="flex cursor-pointer list-none items-center justify-between gap-4 px-5 py-4 transition hover:bg-gray-50">
                            <div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="font-black text-gray-900">{{ $promocion->nombre }}</span>
                                    <span class="rounded-md px-2 py-1 text-xs font-bold {{ $promocion->estado === 'Activo' ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-600' }}">{{ $promocion->estado }}</span>
                                </div>
                                <p class="mt-1 text-sm text-gray-500">
                                    {{ $promocion->tipo_descuento === 'Monto' ? 'S/ ' . number_format((float) $promocion->valor_descuento, 2) : number_format((float) $promocion->valor_descuento, 0) . '%' }}
                                    sobre {{ count($selectedProducts) }} producto(s) y {{ count($selectedCategories) }} categoria(s)
                                </p>
                            </div>
                            <i class="fas fa-chevron-down text-gray-400 transition group-open:rotate-180"></i>
                        </summary>

                        <div class="bg-gray-50 px-5 py-5">
                            <form method="POST" action="{{ route('admin.promociones.update', $promocion->id_promocion) }}" class="grid gap-4 lg:grid-cols-4">
                                @csrf
                                <div class="lg:col-span-2">
                                    <label class="mb-1 block text-sm font-bold text-gray-700">Nombre</label>
                                    <input name="nombre" value="{{ $promocion->nombre }}" class="w-full rounded-lg border-gray-200 bg-white px-4 py-2.5 focus:border-brand focus:ring-brand" required>
                                </div>
                                <div>
                                    <label class="mb-1 block text-sm font-bold text-gray-700">Tipo</label>
                                    <select name="tipo_descuento" class="w-full rounded-lg border-gray-200 bg-white px-4 py-2.5 focus:border-brand focus:ring-brand">
                                        <option value="Porcentaje" @selected($promocion->tipo_descuento === 'Porcentaje')>Porcentaje</option>
                                        <option value="Monto" @selected($promocion->tipo_descuento === 'Monto')>Monto fijo</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="mb-1 block text-sm font-bold text-gray-700">Valor</label>
                                    <input type="number" step="0.01" min="0.01" name="valor_descuento" value="{{ $promocion->valor_descuento }}" class="w-full rounded-lg border-gray-200 bg-white px-4 py-2.5 focus:border-brand focus:ring-brand" required>
                                </div>
                                <div class="lg:col-span-2">
                                    <label class="mb-1 block text-sm font-bold text-gray-700">Productos</label>
                                    <select name="producto_ids[]" multiple class="h-36 w-full rounded-lg border-gray-200 bg-white px-4 py-2.5 focus:border-brand focus:ring-brand">
                                        @foreach($productos as $producto)
                                            <option value="{{ $producto->id_producto }}" @selected(in_array($producto->id_producto, $selectedProducts, true))>{{ $producto->nombre_base }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="lg:col-span-2">
                                    <label class="mb-1 block text-sm font-bold text-gray-700">Categorias</label>
                                    <select name="categoria_ids[]" multiple class="h-36 w-full rounded-lg border-gray-200 bg-white px-4 py-2.5 focus:border-brand focus:ring-brand">
                                        @foreach($categorias as $categoria)
                                            <option value="{{ $categoria->id_categoria }}" @selected(in_array($categoria->id_categoria, $selectedCategories, true))>{{ $categoria->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="mb-1 block text-sm font-bold text-gray-700">Inicio</label>
                                    <input type="date" name="fecha_inicio" value="{{ optional($promocion->fecha_inicio)->format('Y-m-d') }}" class="w-full rounded-lg border-gray-200 bg-white px-4 py-2.5 focus:border-brand focus:ring-brand">
                                </div>
                                <div>
                                    <label class="mb-1 block text-sm font-bold text-gray-700">Fin</label>
                                    <input type="date" name="fecha_fin" value="{{ optional($promocion->fecha_fin)->format('Y-m-d') }}" class="w-full rounded-lg border-gray-200 bg-white px-4 py-2.5 focus:border-brand focus:ring-brand">
                                </div>
                                <div>
                                    <label class="mb-1 block text-sm font-bold text-gray-700">Estado</label>
                                    <select name="estado" class="w-full rounded-lg border-gray-200 bg-white px-4 py-2.5 focus:border-brand focus:ring-brand">
                                        <option value="Activo" @selected($promocion->estado === 'Activo')>Activo</option>
                                        <option value="Inactivo" @selected($promocion->estado === 'Inactivo')>Inactivo</option>
                                    </select>
                                </div>
                                <div class="flex items-end gap-2">
                                    <button class="flex-1 rounded-lg bg-gray-900 px-4 py-2.5 font-bold text-white transition hover:bg-brand">Actualizar</button>
                                </div>
                                <div class="lg:col-span-4">
                                    <label class="mb-1 block text-sm font-bold text-gray-700">Descripcion</label>
                                    <textarea name="descripcion" rows="2" class="w-full rounded-lg border-gray-200 bg-white px-4 py-2.5 focus:border-brand focus:ring-brand">{{ $promocion->descripcion }}</textarea>
                                </div>
                            </form>
                            <form method="POST" action="{{ route('admin.promociones.delete', $promocion->id_promocion) }}" class="mt-3" onsubmit="return confirm('Eliminar esta promocion?');">
                                @csrf
                                <button class="rounded-lg border border-red-200 bg-white px-4 py-2 text-sm font-bold text-red-600 transition hover:bg-red-50">Eliminar promocion</button>
                            </form>
                        </div>
                    </details>
                @empty
                    <div class="px-5 py-16 text-center text-gray-500">No hay promociones registradas.</div>
                @endforelse
            </div>

            <div class="border-t border-gray-100 px-5 py-4">
                {{ $promociones->links() }}
            </div>
        </section>
    </div>
@endsection
