@extends('layouts.admin')

@section('title', 'Categorias')
@section('page-title', 'Categorias y Subcategorias')
@section('page-kicker', 'Modulo de tienda')

@section('topbar-actions')
    <a href="{{ route('storefront.index') }}" class="topbar-badge" style="text-decoration:none;">
        <i class="fas fa-store"></i> Ver tienda
    </a>
@endsection

@section('content')
    <div class="max-w-7xl mx-auto w-full" x-data="{ modalOpen: false, editMode: false, currentItem: {nombre:'', id_categoria_padre:'', estado:'Activo'} }">
        <div class="flex flex-col md:flex-row md:justify-between md:items-end gap-4 mb-6">
            <div>
                <h2 class="text-2xl font-extrabold text-gray-900">Arbol del minimarket y cafeteria</h2>
                <p class="text-gray-500 mt-1">Crea categorias padre, subcategorias y niveles internos para separar cafe, panaderia, abarrotes, snacks y basicos.</p>
            </div>
            <button @click="editMode = false; currentItem = {nombre:'', id_categoria_padre:'', estado:'Activo'}; modalOpen = true" class="bg-brand hover:bg-brand-dark text-white px-5 py-2.5 rounded-xl font-bold shadow-lg shadow-brand/30 transition-all flex items-center justify-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12m6-6H6"></path></svg>
                Nueva Categoria
            </button>
        </div>

        @if($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-r-xl shadow-sm mb-6">
                <ul class="list-disc pl-5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <section class="lg:col-span-2 space-y-4">
                @forelse($categoriasTree as $root)
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                        <div class="p-5 bg-gray-50 border-b border-gray-100 flex items-center justify-between gap-4">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="w-9 h-9 rounded-xl bg-brand/10 text-brand flex items-center justify-center">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7a2 2 0 012-2h4l2 2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V7z"></path></svg>
                                    </span>
                                    <div>
                                        <h3 class="font-extrabold text-gray-900">{{ $root->nombre }}</h3>
                                        <p class="text-xs text-gray-500">{{ $root->estado }} · {{ $root->productos_count }} producto(s)</p>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <button @click='editMode = false; currentItem = {nombre:"", id_categoria_padre:"{{ $root->id_categoria }}", estado:"Activo"}; modalOpen = true' class="px-3 py-2 text-sm font-bold rounded-lg bg-brand/10 text-brand hover:bg-brand hover:text-white">Sub</button>
                                <button @click="editMode = true; currentItem = @js(['id' => $root->id_categoria, 'nombre' => $root->nombre, 'id_categoria_padre' => $root->id_categoria_padre, 'estado' => $root->estado]); modalOpen = true" class="px-3 py-2 text-sm font-bold rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200">Editar</button>
                            </div>
                        </div>

                        @if($root->hijos->count() > 0)
                            <div class="divide-y divide-gray-100">
                                @foreach($root->hijos as $child)
                                    <div class="pl-8 pr-5 py-4 flex items-center justify-between gap-4">
                                        <div class="flex items-center gap-3">
                                            <span class="w-6 h-6 border-l-2 border-b-2 border-gray-300 rounded-bl-lg"></span>
                                            <div>
                                                <p class="font-bold text-gray-800">{{ $child->nombre }}</p>
                                                <p class="text-xs text-gray-500">{{ $child->estado }} · Subcategoria</p>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <button @click='editMode = false; currentItem = {nombre:"", id_categoria_padre:"{{ $child->id_categoria }}", estado:"Activo"}; modalOpen = true' class="px-3 py-2 text-sm font-bold rounded-lg bg-orange-50 text-brand hover:bg-brand hover:text-white">Sub</button>
                                            <button @click="editMode = true; currentItem = @js(['id' => $child->id_categoria, 'nombre' => $child->nombre, 'id_categoria_padre' => $child->id_categoria_padre, 'estado' => $child->estado]); modalOpen = true" class="px-3 py-2 text-sm font-bold rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200">Editar</button>
                                        </div>
                                    </div>

                                    @foreach($child->hijos as $grandChild)
                                        <div class="pl-16 pr-5 py-3 bg-gray-50/60 flex items-center justify-between gap-4">
                                            <div class="flex items-center gap-3">
                                                <span class="w-6 h-6 border-l-2 border-b-2 border-dashed border-gray-300 rounded-bl-lg"></span>
                                                <div>
                                                    <p class="font-semibold text-gray-700">{{ $grandChild->nombre }}</p>
                                                    <p class="text-xs text-gray-500">{{ $grandChild->estado }} · Nivel interno</p>
                                                </div>
                                            </div>
                                            <button @click="editMode = true; currentItem = @js(['id' => $grandChild->id_categoria, 'nombre' => $grandChild->nombre, 'id_categoria_padre' => $grandChild->id_categoria_padre, 'estado' => $grandChild->estado]); modalOpen = true" class="px-3 py-2 text-sm font-bold rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200">Editar</button>
                                        </div>
                                    @endforeach
                                @endforeach
                            </div>
                        @else
                            <div class="p-5 text-sm text-gray-500">Sin subcategorias todavia.</div>
                        @endif
                    </div>
                @empty
                    <div class="bg-white rounded-2xl border border-dashed border-gray-200 p-12 text-center text-gray-500">
                        No hay categorias registradas.
                    </div>
                @endforelse
            </section>

            <aside class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 h-fit">
                <h3 class="font-extrabold text-gray-900 mb-4">Todas las categorias</h3>
                <div class="space-y-2 max-h-[560px] overflow-y-auto pr-1">
                    @foreach($categorias as $cat)
                        <div class="p-3 border border-gray-100 rounded-xl">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="font-bold text-gray-800">{{ $cat->nombre }}</p>
                                    <p class="text-xs text-gray-500">{{ $cat->padre ? 'Sub de: '.$cat->padre->nombre : 'Categoria padre' }}</p>
                                    <p class="text-xs text-gray-400">{{ $cat->productos_count }} producto(s), {{ $cat->hijos_count }} subcategoria(s)</p>
                                </div>
                                <span class="text-xs px-2 py-1 rounded-full {{ $cat->estado === 'Activo' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">{{ $cat->estado }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </aside>
        </div>

    <div x-show="modalOpen" class="fixed inset-0 z-[200] overflow-y-auto" style="display: none;">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="modalOpen" @click="modalOpen = false" x-transition.opacity class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm"></div>
            </div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div x-show="modalOpen" x-transition class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl w-full">
                <form :action="editMode ? '{{ url()->to('admin/categorias') }}/' + currentItem.id + '/update' : '{{ route('admin.categorias.store') }}'" method="POST">
                    @csrf
                    <div class="bg-white px-6 pt-6 pb-4 sm:p-8 sm:pb-6">
                        <div class="flex items-center mb-6 border-b border-gray-100 pb-4">
                            <div class="flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-orange-50 mr-4">
                                <svg class="h-6 w-6 text-brand" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7a2 2 0 012-2h4l2 2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V7z"></path></svg>
                            </div>
                            <h3 class="text-xl leading-6 font-extrabold text-gray-900" x-text="editMode ? 'Editar Categoria' : 'Nueva Categoria'"></h3>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Nombre <span class="text-red-500">*</span></label>
                                <input type="text" name="nombre" x-model="currentItem.nombre" required class="w-full border-gray-200 rounded-xl px-4 py-2.5 bg-gray-50 focus:bg-white focus:border-brand focus:ring-brand outline-none transition-all" placeholder="Ej. Jugos naturales">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Categoria padre</label>
                                <select name="id_categoria_padre" x-model="currentItem.id_categoria_padre" class="w-full border-gray-200 rounded-xl px-4 py-2.5 bg-gray-50 focus:bg-white focus:border-brand focus:ring-brand outline-none transition-all">
                                    <option value="">Es categoria principal</option>
                                    @foreach($categorias as $cat)
                                        <option value="{{ $cat->id_categoria }}">{{ $cat->padre ? $cat->padre->nombre.' / ' : '' }}{{ $cat->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Estado</label>
                                <select name="estado" x-model="currentItem.estado" required class="w-full border-gray-200 rounded-xl px-4 py-2.5 bg-gray-50 focus:bg-white focus:border-brand focus:ring-brand outline-none transition-all">
                                    <option value="Activo">Activo</option>
                                    <option value="Inactivo">Inactivo</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-4 sm:px-6 flex flex-wrap justify-between gap-3 rounded-b-3xl">
                        <template x-if="editMode">
                            <button type="submit" :formaction="'{{ url()->to('admin/categorias') }}/' + currentItem.id + '/delete'" onclick="return confirm('¿Seguro de eliminar esta categoria?')" class="bg-red-50 text-red-600 hover:bg-red-100 px-5 py-2.5 rounded-xl font-bold transition-colors">
                                Eliminar
                            </button>
                        </template>
                        <div class="ml-auto flex gap-3">
                            <button type="button" @click="modalOpen = false" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 px-5 py-2.5 rounded-xl font-bold transition-colors">
                                Cancelar
                            </button>
                            <button type="submit" class="bg-brand hover:bg-brand-dark text-white px-6 py-2.5 rounded-xl font-bold transition-all shadow-md shadow-brand/20">
                                Guardar
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    </div>
@endsection
