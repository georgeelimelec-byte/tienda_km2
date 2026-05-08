@extends('layouts.admin')

@section('title', 'Banners')
@section('page-title', 'Banners y Promociones')
@section('page-kicker', 'Modulo de tienda')

@section('topbar-actions')
    <a href="{{ route('storefront.index') }}" class="topbar-badge" style="text-decoration:none;">
        <i class="fas fa-store"></i> Ver tienda
    </a>
@endsection

@section('content')
    <div class="max-w-7xl mx-auto w-full" x-data="{ modalOpen: false, editMode: false, currentItem: {titulo:'', imagen_url:'', link_destino:'/', posicion:'Carrusel', estado:'Activo'} }">
        <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 mb-6">
            <div>
                <h2 class="text-2xl font-extrabold text-gray-900">Banners de tienda</h2>
                <p class="text-gray-500 mt-1">Actualiza imagenes, titulos, enlaces, ubicacion y estado de los banners de tienda.</p>
                <p class="text-sm text-gray-500 mt-2">
                    {{ $banners->where('estado', 'Activo')->count() }} activo(s) · {{ $banners->where('estado', 'Inactivo')->count() }} inactivo(s)
                </p>
            </div>
            <button @click="editMode = false; currentItem = {titulo:'', imagen_url:'', link_destino:'/', posicion:'Carrusel', estado:'Activo'}; modalOpen = true" class="bg-brand hover:bg-brand-dark text-white px-5 py-2.5 rounded-lg font-bold shadow-lg shadow-brand/20 transition-all flex items-center justify-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12m6-6H6"></path></svg>
                Nuevo banner
            </button>
        </div>

        @if($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-r-lg shadow-sm mb-6">
                <ul class="list-disc pl-5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <section class="lg:col-span-2 bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-5 py-4 text-left text-xs font-bold uppercase text-gray-500">Imagen</th>
                                <th class="px-5 py-4 text-left text-xs font-bold uppercase text-gray-500">Contenido</th>
                                <th class="px-5 py-4 text-left text-xs font-bold uppercase text-gray-500">Estado</th>
                                <th class="px-5 py-4 text-right text-xs font-bold uppercase text-gray-500">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($banners as $banner)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-5 py-4">
                                        <img src="{{ $banner->imagen_url }}" alt="{{ $banner->titulo }}" class="h-20 w-36 rounded-md object-cover border border-gray-200">
                                    </td>
                                    <td class="px-5 py-4">
                                        <p class="font-bold text-gray-900">{{ $banner->titulo }}</p>
                                        <p class="text-xs text-gray-500">{{ str_replace('_', ' ', $banner->posicion) }} · {{ $banner->link_destino ?: '/' }}</p>
                                    </td>
                                    <td class="px-5 py-4">
                                        <span class="inline-flex px-3 py-1 rounded-md text-xs font-bold {{ $banner->estado === 'Activo' ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700' }}">{{ $banner->estado }}</span>
                                    </td>
                                    <td class="px-5 py-4 text-right">
                                        <button @click="editMode = true; currentItem = @js([
                                            "id" => $banner->id_banner,
                                            "titulo" => $banner->titulo,
                                            "imagen_url" => $banner->imagen_url,
                                            "link_destino" => $banner->link_destino,
                                            "posicion" => $banner->posicion,
                                            "estado" => $banner->estado,
                                        ]); modalOpen = true" class="text-brand hover:text-brand-dark font-bold text-sm mr-3">Editar</button>
                                        <form action="{{ route('admin.banners.delete', $banner->id_banner) }}" method="POST" class="inline" onsubmit="return confirm('Eliminar este banner?');">
                                            @csrf
                                            <button type="submit" class="text-red-500 hover:text-red-700 font-bold text-sm">Eliminar</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-5 py-12 text-center text-gray-500">No hay banners configurados.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <aside class="bg-white rounded-lg border border-gray-200 shadow-sm p-5 h-fit">
                <h3 class="font-extrabold text-gray-900 mb-2">Como se usa</h3>
                <p class="text-sm text-gray-600 mb-4">Los banners activos pueden mostrarse como carrusel, oferta lateral o pop-up promocional.</p>
                <div class="space-y-3 text-sm text-gray-600">
                    <div class="rounded-lg border border-gray-200 p-3">
                        <p class="font-bold text-gray-900">Imagen recomendada</p>
                        <p>Formato horizontal 1800 x 900 o superior.</p>
                    </div>
                    <div class="rounded-lg border border-gray-200 p-3">
                        <p class="font-bold text-gray-900">Animacion del carrusel</p>
                        <p>La tienda aplica transiciones, zoom suave y progreso automaticamente.</p>
                    </div>
                </div>
            </aside>
        </div>

    <div x-show="modalOpen" class="fixed inset-0 z-[200] overflow-y-auto" style="display: none;">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="modalOpen" @click="modalOpen = false" x-transition.opacity class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm"></div>
            </div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div x-show="modalOpen" x-transition class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl w-full">
                <form :action="editMode ? '{{ url()->to('admin/banners') }}/' + currentItem.id + '/update' : '{{ route('admin.banners.store') }}'" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="bg-white px-6 pt-6 pb-4 sm:p-8">
                        <div class="flex items-center justify-between mb-6 border-b border-gray-100 pb-4">
                            <h3 class="text-xl font-extrabold text-gray-900" x-text="editMode ? 'Editar banner' : 'Nuevo banner'"></h3>
                            <template x-if="currentItem.imagen_url">
                                <img :src="currentItem.imagen_url" alt="" class="h-14 w-24 rounded-md object-cover border border-gray-200">
                            </template>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Titulo <span class="text-red-500">*</span></label>
                                <input type="text" name="titulo" x-model="currentItem.titulo" required maxlength="100" class="w-full border-gray-200 rounded-lg px-4 py-2.5 bg-gray-50 focus:bg-white focus:border-brand focus:ring-brand outline-none transition-all">
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">URL de imagen</label>
                                <input type="url" name="imagen_url" x-model="currentItem.imagen_url" class="w-full border-gray-200 rounded-lg px-4 py-2.5 bg-gray-50 focus:bg-white focus:border-brand focus:ring-brand outline-none transition-all" placeholder="https://...">
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Subir imagen</label>
                                <input type="file" name="imagen_archivo" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 bg-gray-50 text-sm">
                                <p class="text-xs text-gray-500 mt-1">Si subes archivo, reemplaza la URL anterior.</p>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Link destino</label>
                                <input type="text" name="link_destino" x-model="currentItem.link_destino" class="w-full border-gray-200 rounded-lg px-4 py-2.5 bg-gray-50 focus:bg-white focus:border-brand focus:ring-brand outline-none transition-all" placeholder="/?categoria_id=5">
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Posicion</label>
                                    <select name="posicion" x-model="currentItem.posicion" class="w-full border-gray-200 rounded-lg px-4 py-2.5 bg-gray-50 focus:bg-white focus:border-brand focus:ring-brand outline-none transition-all">
                                        <option value="Carrusel">Carrusel</option>
                                        <option value="Lateral">Lateral</option>
                                        <option value="Pop_up">Pop-up</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Estado</label>
                                    <select name="estado" x-model="currentItem.estado" required class="w-full border-gray-200 rounded-lg px-4 py-2.5 bg-gray-50 focus:bg-white focus:border-brand focus:ring-brand outline-none transition-all">
                                        <option value="Activo">Activo</option>
                                        <option value="Inactivo">Inactivo</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-4 sm:px-6 flex justify-end gap-3">
                        <button type="button" @click="modalOpen = false" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 px-5 py-2.5 rounded-lg font-bold transition-colors">
                            Cancelar
                        </button>
                        <button type="submit" class="bg-brand hover:bg-brand-dark text-white px-6 py-2.5 rounded-lg font-bold transition-all shadow-md shadow-brand/20">
                            Guardar banner
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    </div>
@endsection
