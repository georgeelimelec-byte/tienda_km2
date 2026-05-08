@extends('layouts.admin')

@section('title', 'Productos')
@section('page-title', 'Catalogo de Productos')
@section('page-kicker', 'Modulo de tienda')

@section('topbar-actions')
    <span class="topbar-badge"><i class="fas fa-boxes-stacked"></i> Stock integrado</span>
@endsection

@push('scripts')
<script>
    function adminProductModal() {
        return {
            modalOpen: false,
            editMode: false,
            currentItem: {},
            selectedImagePreview: null,
            selectedImageName: '',

            baseItem() {
                return {
                    nombre: '',
                    descripcion: '',
                    id_categoria: '',
                    precio_venta: '',
                    precio_oferta: '',
                    stock: 0,
                    galeria_urls: '',
                    nombre_variante: 'Unidad',
                    codigo_barras: '',
                    estado: 'Activo',
                };
            },

            openCreate() {
                this.clearSelectedImage();
                this.editMode = false;
                this.currentItem = this.baseItem();
                this.modalOpen = true;
            },

            openEdit(item) {
                this.clearSelectedImage();
                this.editMode = true;
                this.currentItem = { ...this.baseItem(), ...item };
                this.modalOpen = true;
            },

            previewSelectedImage(event) {
                const file = event.target.files && event.target.files[0] ? event.target.files[0] : null;

                if (this.selectedImagePreview) {
                    URL.revokeObjectURL(this.selectedImagePreview);
                    this.selectedImagePreview = null;
                }

                if (!file || !file.type.startsWith('image/')) {
                    this.selectedImageName = '';
                    return;
                }

                this.selectedImageName = file.name;
                this.selectedImagePreview = URL.createObjectURL(file);
            },

            clearSelectedImage() {
                if (this.selectedImagePreview) {
                    URL.revokeObjectURL(this.selectedImagePreview);
                }

                this.selectedImagePreview = null;
                this.selectedImageName = '';

                if (this.$refs.generalImageInput) {
                    this.$refs.generalImageInput.value = '';
                }
            },

            get galleryPreviewUrls() {
                return String(this.currentItem.galeria_urls || '')
                    .split(/[\r\n,]+/)
                    .map((url) => url.trim())
                    .filter(Boolean);
            },

            removeGalleryUrl(index) {
                const urls = this.galleryPreviewUrls;
                urls.splice(index, 1);
                this.currentItem.galeria_urls = urls.join('\n');
            },
        };
    }
</script>
@endpush

@section('content')
    <div class="max-w-7xl mx-auto w-full" x-data="adminProductModal()">
        
        <div class="flex justify-between items-end mb-6">
            <div>
                <h2 class="text-2xl font-extrabold text-gray-900">Productos en Vitrina</h2>
                <p class="text-gray-500 mt-1">Sube, edita o desactiva los productos que se mostrarán en la tienda.</p>
            </div>
            <button @click="openCreate()" class="bg-brand hover:bg-brand-dark text-white px-5 py-2.5 rounded-lg font-bold shadow-lg shadow-brand/20 transition-all flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                Subir Producto
            </button>
        </div>

        @if($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-r-xl shadow-sm mb-6" role="alert">
                <ul class="list-disc pl-5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white rounded-[1.5rem] shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50/50">
                        <tr>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">SKU / Imagen</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Detalles del Producto</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Stock</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Estado</th>
                            <th scope="col" class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse($productos as $item)
                        @php
                            $presentacion = $item->presentaciones->first();
                            $imgPrincipal = $item->imagen_principal_url;
                            $precioRegular = $presentacion ? (float) $presentacion->precio : 0;
                            $precioOferta = $presentacion && $presentacion->precio_oferta !== null ? (float) $presentacion->precio_oferta : null;
                            $precio = $presentacion ? (float) $presentacion->precio_efectivo : 0;
                            $stock = $presentacion ? (int) $presentacion->stock : 0;
                            $galleryUrls = $item->imagenes->pluck('imagen_url')->implode("\n");
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-12 w-12 bg-gray-100 rounded-lg flex items-center justify-center p-1">
                                        <img class="max-h-full max-w-full object-contain" src="{{ $imgPrincipal }}" alt="">
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm text-gray-500 font-mono">{{ $item->sku ?? '#000' . $item->id_producto }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-900 line-clamp-1" title="{{ $item->nombre_base }}">{{ $item->nombre_base }}</div>
                                <div class="text-xs text-gray-500">{{ $item->categoria->nombre ?? 'Sin categoria' }}</div>
                                <div class="text-sm font-bold text-brand mt-1">S/ {{ number_format($precio, 2) }}</div>
                                @if($precioOferta !== null && $precioOferta < $precioRegular)
                                    <div class="text-xs text-gray-400 line-through">Regular S/ {{ number_format($precioRegular, 2) }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-semibold text-gray-700">{{ $stock }} unid.</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $item->estado == 'Activo' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $item->estado }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button @click="openEdit(@js([
                                    "id" => $item->id_producto,
                                    "nombre" => $item->nombre_base,
                                    "descripcion" => $item->descripcion,
                                    "id_categoria" => $item->id_categoria,
                                    "precio_venta" => $precioRegular,
                                    "precio_oferta" => $precioOferta,
                                    "stock" => $stock,
                                    "estado" => $item->estado,
                                    "galeria_urls" => $galleryUrls,
                                    "nombre_variante" => $presentacion->nombre_variante ?? 'Unidad',
                                    "codigo_barras" => $presentacion->codigo_barras ?? '',
                                ]))" class="text-brand hover:text-brand-dark mr-3 transition-colors">
                                    Editar
                                </button>
                                <form action="{{ route('admin.productos.delete', $item->id_producto) }}" method="POST" class="inline" onsubmit="return confirm('¿Seguro de eliminar este producto?');">
                                    @csrf
                                    <button type="submit" class="text-red-500 hover:text-red-700 transition-colors">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-gray-500 bg-gray-50/50">
                                No hay productos registrados aún.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-3 border-t border-gray-100">
                {{ $productos->links() ?? '' }}
            </div>
        </div>

    <!-- Modal Alpine -->
    <div x-show="modalOpen" class="fixed inset-0 z-[200] overflow-y-auto" style="display: none;">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="modalOpen" @click="modalOpen = false" x-transition.opacity class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm"></div>
            </div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="modalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl w-full">
                
                <form :action="editMode ? '{{ url()->to('admin/productos') }}/' + currentItem.id + '/update' : '{{ route('admin.productos.store') }}'" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="bg-white px-6 pt-6 pb-4 sm:p-8 sm:pb-6">
                        <div class="flex items-center mb-6 border-b border-gray-100 pb-4">
                            <div class="flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-50 mr-4">
                                <svg class="h-6 w-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                            </div>
                            <h3 class="text-xl leading-6 font-extrabold text-gray-900" x-text="editMode ? 'Editar Producto' : 'Crear Nuevo Producto'"></h3>
                        </div>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Nombre Comercial <span class="text-red-500">*</span></label>
                                <input type="text" name="nombre" x-model="currentItem.nombre" required class="w-full border-gray-200 rounded-xl px-4 py-2.5 bg-gray-50 focus:bg-white focus:border-brand focus:ring-brand outline-none transition-all" placeholder="Ej. iPhone 15 Pro Max">
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Descripcion</label>
                                <textarea name="descripcion" x-model="currentItem.descripcion" rows="2" class="w-full border-gray-200 rounded-xl px-4 py-2.5 bg-gray-50 focus:bg-white focus:border-brand focus:ring-brand outline-none transition-all" placeholder="Descripcion visible en la tienda"></textarea>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Categoría <span class="text-red-500">*</span></label>
                                    <select name="id_categoria" x-model="currentItem.id_categoria" required class="w-full border-gray-200 rounded-xl px-4 py-2.5 bg-gray-50 focus:bg-white focus:border-brand focus:ring-brand outline-none transition-all">
                                        <option value="">Seleccione...</option>
                                        @foreach($categorias as $cat)
                                            <option value="{{ $cat->id_categoria }}">{{ $cat->padre ? $cat->padre->nombre.' / ' : '' }}{{ $cat->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Precio regular S/ <span class="text-red-500">*</span></label>
                                    <input type="number" step="0.01" name="precio_venta" x-model="currentItem.precio_venta" required class="w-full border-gray-200 rounded-xl px-4 py-2.5 bg-gray-50 focus:bg-white focus:border-brand focus:ring-brand outline-none transition-all" placeholder="0.00">
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Precio oferta S/</label>
                                    <input type="number" step="0.01" name="precio_oferta" x-model="currentItem.precio_oferta" class="w-full border-gray-200 rounded-xl px-4 py-2.5 bg-gray-50 focus:bg-white focus:border-brand focus:ring-brand outline-none transition-all" placeholder="Opcional">
                                    <p class="text-xs text-gray-500 mt-1">Debe ser menor que el precio regular para mostrarse como promocion.</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Presentacion</label>
                                    <input type="text" name="nombre_variante" x-model="currentItem.nombre_variante" class="w-full border-gray-200 rounded-xl px-4 py-2.5 bg-gray-50 focus:bg-white focus:border-brand focus:ring-brand outline-none transition-all" placeholder="Ej. Vaso 12 oz">
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Codigo de barras</label>
                                    <input type="text" name="codigo_barras" x-model="currentItem.codigo_barras" class="w-full border-gray-200 rounded-xl px-4 py-2.5 bg-gray-50 focus:bg-white focus:border-brand focus:ring-brand outline-none transition-all" placeholder="Opcional">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Stock Disponible <span class="text-red-500">*</span></label>
                                    <input type="number" name="stock" x-model="currentItem.stock" required class="w-full border-gray-200 rounded-xl px-4 py-2.5 bg-gray-50 focus:bg-white focus:border-brand focus:ring-brand outline-none transition-all">
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Estado</label>
                                    <select name="estado" x-model="currentItem.estado" class="w-full border-gray-200 rounded-xl px-4 py-2.5 bg-gray-50 focus:bg-white focus:border-brand focus:ring-brand outline-none transition-all">
                                        <option value="Activo">Activo (Visible)</option>
                                        <option value="Inactivo">Inactivo (Oculto)</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Subir imagen general</label>
                                <input x-ref="generalImageInput" @change="previewSelectedImage($event)" type="file" name="foto_archivo" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 bg-gray-50 text-sm">
                                <p class="text-xs text-gray-500 mt-1">Si subes archivo, se usara como imagen principal general. No borra las imagenes propias de cada variante.</p>

                                <template x-if="selectedImagePreview">
                                    <div class="mt-3 flex items-center gap-3 rounded-xl border border-orange-100 bg-orange-50/60 p-3">
                                        <img :src="selectedImagePreview" alt="Vista previa de imagen seleccionada" class="h-16 w-16 rounded-lg border border-white object-cover shadow-sm">
                                        <div class="min-w-0 flex-1">
                                            <p class="truncate text-sm font-bold text-gray-900" x-text="selectedImageName"></p>
                                            <p class="text-xs text-gray-500">Imagen seleccionada para subir como principal general.</p>
                                        </div>
                                        <button type="button" @click="clearSelectedImage()" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-red-100 bg-white text-red-600 transition hover:bg-red-50" title="Quitar imagen seleccionada" aria-label="Quitar imagen seleccionada">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 18 6M6 6l12 12"></path></svg>
                                        </button>
                                    </div>
                                </template>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">URLs de imagenes generales</label>
                                <textarea name="galeria_urls" x-model="currentItem.galeria_urls" rows="3" class="w-full border-gray-200 rounded-xl px-4 py-2.5 bg-gray-50 focus:bg-white focus:border-brand focus:ring-brand outline-none transition-all" placeholder="Una URL por linea. La primera se usara como principal."></textarea>
                                <p class="text-xs text-gray-500 mt-1">La primera URL se usa como imagen principal y como respaldo para presentaciones sin foto propia.</p>

                                <template x-if="galleryPreviewUrls.length">
                                    <div class="mt-3 grid grid-cols-2 gap-3 sm:grid-cols-4">
                                        <template x-for="(url, index) in galleryPreviewUrls" :key="url + index">
                                            <div class="group relative overflow-hidden rounded-xl border border-gray-200 bg-gray-50">
                                                <img :src="url" alt="Imagen general del producto" class="h-24 w-full object-cover">
                                                <button type="button" @click="removeGalleryUrl(index)" class="absolute right-2 top-2 inline-flex h-8 w-8 items-center justify-center rounded-lg bg-white/95 text-red-600 shadow-sm transition hover:bg-red-50" title="Quitar URL" aria-label="Quitar URL">
                                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 18 6M6 6l12 12"></path></svg>
                                                </button>
                                                <div class="absolute bottom-0 left-0 right-0 bg-gray-950/55 px-2 py-1 text-[10px] font-bold uppercase text-white" x-text="index === 0 ? 'Principal' : 'Galeria'"></div>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-4 sm:px-6 flex justify-end gap-3 rounded-b-3xl">
                        <button type="button" @click="modalOpen = false" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 px-5 py-2.5 rounded-xl font-bold transition-colors">
                            Cancelar
                        </button>
                        <button type="submit" class="bg-brand hover:bg-brand-dark text-white px-6 py-2.5 rounded-xl font-bold transition-all shadow-md shadow-brand/20">
                            Guardar Producto
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    </div>
@endsection
