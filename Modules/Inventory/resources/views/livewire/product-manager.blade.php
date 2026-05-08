<div>
    <style>
        .page-toolbar {
            display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; gap: 16px; flex-wrap: wrap;
        }
        .search-box {
            position: relative; flex: 1; max-width: 400px;
        }
        .toolbar-filters {
            display: flex; align-items: center; gap: 12px; flex-wrap: wrap;
        }
        .search-box i {
            position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: var(--text-muted);
        }
        .search-box input {
            width: 100%; padding: 12px 16px 12px 42px; background: #ffffff; border: 1px solid var(--border);
            border-radius: var(--radius); font-size: 14px; font-family: inherit; color: var(--text-primary);
            transition: var(--transition); box-shadow: var(--shadow);
        }
        .search-box input:focus { border-color: var(--primary); outline: none; box-shadow: 0 0 0 4px rgba(229,140,58,0.1); }

        .product-card {
            background: #ffffff; border: 1px solid var(--border); border-radius: var(--radius);
            padding: 20px; margin-bottom: 16px; box-shadow: var(--shadow); transition: var(--transition);
        }
        .product-card:hover { box-shadow: var(--shadow-lg); border-color: rgba(229,140,58,0.2); }
        .product-header { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 12px; }
        .product-title { font-size: 18px; font-weight: 800; color: var(--text-primary); display: flex; align-items: center; gap: 10px; }
        .product-cat { font-size: 12px; background: #f4eee9; color: #a36a32; padding: 4px 10px; border-radius: 20px; font-weight: 600; }
        
        .variant-table {
            width: 100%; border-collapse: separate; border-spacing: 0; margin-top: 10px;
            background: #fdfcfb; border-radius: 12px; overflow: hidden; border: 1px solid var(--border);
        }
        .variant-table th { background: #f9f8f6; font-size: 11px; text-transform: uppercase; color: var(--text-muted); padding: 10px 16px; text-align: left; font-weight: 700; border-bottom: 1px solid var(--border); }
        .variant-table td { padding: 12px 16px; border-bottom: 1px solid var(--border); font-size: 13px; color: var(--text-secondary); vertical-align: middle; }
        .variant-table tr:last-child td { border-bottom: none; }
        
        .badge-stock { background: #dcfce7; color: #166534; padding: 4px 8px; border-radius: 6px; font-weight: 700; font-size: 12px; display: inline-block;}
        .badge-stock.low { background: #fee2e2; color: #991b1b; }

        /* Modal Interno de Producto */
        .modal-body-scroll { max-height: calc(100vh - 200px); overflow-y: auto; padding: 24px; }
        .form-section { background: #f9f8f6; border: 1px solid var(--border); border-radius: 16px; padding: 20px; margin-bottom: 24px; }
        .form-section h4 { margin: 0 0 16px 0; font-size: 15px; color: var(--text-primary); display: flex; align-items: center; gap: 8px; }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .form-group-full { grid-column: span 2; }
        
        .form-label { display: block; font-size: 12px; font-weight: 700; color: var(--text-secondary); margin-bottom: 6px; }
        .form-control { width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: 10px; font-size: 14px; font-family: inherit; background: #ffffff; transition: all 0.2s; }
        .form-control:focus { border-color: var(--primary); outline: none; box-shadow: 0 0 0 3px rgba(229,140,58,0.1); }
        
        .variant-row { display: grid; grid-template-columns: 2fr 1.5fr 1fr 1fr 1fr 1fr 40px; gap: 10px; align-items: end; margin-bottom: 12px; background: #ffffff; padding: 12px; border: 1px solid var(--border); border-radius: 12px; }
        .variant-row-header { display: grid; grid-template-columns: 2fr 1.5fr 1fr 1fr 1fr 1fr 40px; gap: 10px; padding: 0 12px; margin-bottom: 8px; }
        .variant-col-label { font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; }
        .variant-image-tools { grid-column: 1 / -1; display: grid; grid-template-columns: minmax(220px, 1fr) minmax(220px, 1fr); gap: 12px; padding-top: 12px; border-top: 1px dashed var(--border); }
        .variant-image-note { font-size: 11px; color: var(--text-muted); margin-top: 5px; line-height: 1.4; }
        .variant-image-preview-grid { grid-column: 1 / -1; display: flex; flex-wrap: wrap; gap: 10px; margin-top: 10px; }
        .variant-image-preview { width: 86px; }
        .variant-image-preview-frame { position: relative; width: 86px; height: 86px; border: 1px solid var(--border); border-radius: 12px; overflow: hidden; background: #f9f8f6; box-shadow: 0 6px 18px rgba(15, 23, 42, 0.08); }
        .variant-image-preview-frame img { width: 100%; height: 100%; object-fit: cover; display: block; }
        .variant-image-preview-remove { position: absolute; top: 6px; right: 6px; width: 24px; height: 24px; display: inline-flex; align-items: center; justify-content: center; border: none; border-radius: 8px; background: rgba(220,38,38,0.92); color: #ffffff; cursor: pointer; box-shadow: 0 4px 12px rgba(0,0,0,0.18); }
        .variant-image-preview-remove:hover { background: #b91c1c; }
        .variant-image-preview-label { margin-top: 5px; font-size: 10px; font-weight: 700; color: var(--text-muted); text-align: center; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .variant-photo-cell { display: flex; align-items: center; gap: 8px; }
        .variant-photo-thumb { width: 42px; height: 42px; border-radius: 8px; object-fit: cover; border: 1px solid var(--border); background: #f9f8f6; }
        .variant-photo-empty { width: 42px; height: 42px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; background: #f9f8f6; color: var(--text-muted); border: 1px dashed var(--border); }
        @media (max-width: 980px) {
            .variant-row, .variant-row-header { grid-template-columns: 1fr; }
            .variant-row-header { display: none; }
            .variant-image-tools { grid-template-columns: 1fr; }
        }
        .btn-remove { width: 36px; height: 36px; display: inline-flex; align-items: center; justify-content: center; background: #fee2e2; color: #dc2626; border-radius: 10px; border: none; cursor: pointer; transition: all 0.2s; }
        .btn-remove:hover { background: #f87171; color: white; }
        
        .btn-add-variant { display: inline-flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 600; color: var(--primary); background: rgba(229,140,58,0.1); padding: 8px 16px; border-radius: 10px; border: none; cursor: pointer; transition: all 0.2s; }
        .btn-add-variant:hover { background: var(--primary); color: white; }

        .form-error { font-size: 12px; color: #dc2626; margin-top: 4px; display: block; }
        .text-danger { color: #dc2626 !important; }
    </style>

    @if (session()->has('message'))
        <div style="background: #dcfce7; color: #166534; padding: 16px; border-radius: 12px; margin-bottom: 24px; font-weight: 600; display: flex; align-items: center; gap: 10px; border: 1px solid #bbf7d0;">
            <i class="fas fa-check-circle"></i> {{ session('message') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div style="background: #fee2e2; color: #991b1b; padding: 16px; border-radius: 12px; margin-bottom: 24px; font-weight: 600; display: flex; align-items: center; gap: 10px; border: 1px solid #fecaca;">
            <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
        </div>
    @endif
    @if ($errors->any())
        <div style="background: #fee2e2; color: #991b1b; padding: 16px; border-radius: 12px; margin-bottom: 24px; font-weight: 600; border: 1px solid #fecaca;">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 8px;">
                <i class="fas fa-exclamation-triangle"></i> Revisa los archivos o datos ingresados.
            </div>
            <ul style="margin: 0; padding-left: 22px; font-weight: 500;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="page-toolbar">
        <div class="toolbar-filters">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Buscar producto o código de variante...">
            </div>
        </div>
        <button wire:click="create" class="btn btn-primary" style="padding: 12px 24px;">
            <i class="fas fa-plus"></i> Nuevo Producto
        </button>
    </div>

    <!-- MAIN PRODUCT LIST -->
    <div>
        @forelse ($products as $prod)
            <div class="product-card">
                <div class="product-header">
                    <div>
                        <div class="product-title">
                            {{ $prod->nombre_base }}
                            <span class="product-cat">{{ $prod->categoria->nombre ?? 'Sin Categoria' }}</span>
                        </div>
                        <div style="font-size: 13px; color: var(--text-muted); margin-top: 4px;">{{ $prod->descripcion ?: 'Sin descripción' }}</div>
                    </div>
                    <div style="display: flex; gap: 8px;">
                        <button wire:click="edit({{ $prod->id_producto }})" class="btn-ghost" style="padding: 8px 12px; border-radius: 8px;">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button onclick="confirm('¿Estás seguro de purgar el producto {{ $prod->nombre_base }}?') || event.stopImmediatePropagation()" wire:click="delete({{ $prod->id_producto }})" class="btn-ghost text-danger" style="padding: 8px 12px; border-radius: 8px;">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                </div>

                <table class="variant-table">
                    <thead>
                        <tr>
                            <th>Variante</th>
                            <th>SKU / Código</th>
                            <th>Unidad</th>
                            <th>Costo S/</th>
                            <th>Precio S/</th>
                            <th>Stock</th>
                            <th>Fotos</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($prod->presentaciones as $pres)
                            @php
                                $variantImage = $pres->imagenes->first();
                            @endphp
                            <tr>
                                <td style="font-weight: 600; color: var(--text-primary);">{{ $pres->nombre_variante }}</td>
                                <td style="font-family: monospace; background: rgba(0,0,0,0.03); padding: 4px 8px; border-radius: 4px;">{{ $pres->codigo_barras ?: '---' }}</td>
                                <td>{{ $pres->unidadMedida->nombre ?? 'Otr' }}</td>
                                <td>{{ number_format($pres->costo_reposicion, 2) }}</td>
                                <td style="font-weight: 700; color: var(--primary);">{{ number_format($pres->precio, 2) }}</td>
                                <td>
                                    @if ($pres->stock <= $pres->stock_minimo)
                                        <span class="badge-stock low">{{ $pres->stock }} <i class="fas fa-arrow-down" style="font-size:10px;"></i></span>
                                    @else
                                        <span class="badge-stock">{{ $pres->stock }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="variant-photo-cell">
                                        @if($variantImage)
                                            <img class="variant-photo-thumb" src="{{ $variantImage->url }}" alt="{{ $pres->nombre_variante }}">
                                            <span>{{ $pres->imagenes->count() }}</span>
                                        @else
                                            <span class="variant-photo-empty"><i class="fas fa-image"></i></span>
                                            <span>0</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @empty
            <div style="text-align: center; padding: 60px; background: #ffffff; border-radius: 16px; border: 1px dashed var(--border);">
                <div style="font-size: 48px; color: var(--border); margin-bottom: 16px;"><i class="fas fa-box-open"></i></div>
                <h3 style="font-size: 18px; color: var(--text-primary); margin-bottom: 8px;">Inventario Vacío</h3>
                <p style="color: var(--text-muted); margin-bottom: 24px;">No se encontraron productos registrados.</p>
                <button wire:click="create" class="btn btn-primary" style="padding: 10px 20px;">Agregar el primero</button>
            </div>
        @endforelse

        <div style="margin-top: 24px;">
            {{ $products->links() }}
        </div>
    </div>

    <!-- MODAL PRODUCT MASTER-DETAIL -->
    @if($isModalOpen)
        <div class="admin-panel-modal-overlay is-open"></div>
        <div class="admin-panel-modal is-open" aria-hidden="false" style="padding: 24px;">
            <!-- Extendimos el maxWidth a 900px para el CRUD y quitamos transform base -->
            <div class="admin-panel-modal-card" style="max-width: 900px; transform: scale(1); animation: modalFadeIn 0.2s ease-out;">
                <style> @keyframes modalFadeIn { from{ opacity: 0; transform: translateY(20px); } to { opacity:1; transform: translateY(0); } } </style>
                <div class="admin-panel-modal-header" style="background: linear-gradient(135deg, #e58c3a, #f7b752); border-bottom: none;">
                    <div>
                        <div class="admin-panel-modal-title" style="color:#ffffff;">{{ $productId ? 'Editar Producto' : 'Nuevo Producto' }}</div>
                        <div class="admin-panel-modal-subtitle" style="color:rgba(255,255,255,0.9);">Configura la información maestra y sus variaciones.</div>
                    </div>
                    <button wire:click="$set('isModalOpen', false)" class="admin-panel-modal-close" style="background: rgba(255,255,255,0.2); border: none; color: white;">
                        <i class="fas fa-xmark"></i>
                    </button>
                </div>
                
                <div class="modal-body-scroll">
                    <!-- SECTION 1: MASTER INFO -->
                    <div class="form-section">
                        <h4><i class="fas fa-cube text-primary"></i> 1. Información Base del Producto</h4>
                        <div class="form-grid">
                            <div>
                                <label class="form-label">Nombre del Producto (Maestro)</label>
                                <input type="text" class="form-control" wire:model="nombre_base" placeholder="Ej: Arroz Costeño">
                                @error('nombre_base') <span class="form-error">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="form-label">Categoría</label>
                                <select class="form-control" wire:model="id_categoria">
                                    <option value="">-- Seleccione Categoría --</option>
                                    @foreach($categorias as $cat)
                                        <option value="{{ $cat->id_categoria }}">{{ $cat->nombre }}</option>
                                    @endforeach
                                </select>
                                @error('id_categoria') <span class="form-error">{{ $message }}</span> @enderror
                            </div>
                            <div class="form-group-full">
                                <label class="form-label">Descripción Detallada (Opcional)</label>
                                <textarea class="form-control" wire:model="descripcion" rows="2" placeholder="Detalles genéricos sobre el producto principal..."></textarea>
                                @error('descripcion') <span class="form-error">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- SECTION 2: VARIANTS BUILDER -->
                    <div class="form-section" style="background: #ffffff; border-color: rgba(229,140,58,0.2); box-shadow: 0 4px 20px rgba(0,0,0,0.02)">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                            <h4 style="margin:0;"><i class="fas fa-tags text-primary"></i> 2. Variantes y Precios (Presentaciones)</h4>
                            <button wire:click.prevent="addPresentacion" class="btn-add-variant">
                                <i class="fas fa-plus"></i> Añadir Presentación
                            </button>
                        </div>
                        
                        @error('presentaciones')
                            <div class="form-error" style="margin-bottom: 16px; font-size: 14px; background: #fee2e2; padding: 12px; border-radius: 8px;">
                                <i class="fas fa-exclamation-triangle"></i> {{ $message }}
                            </div>
                        @enderror

                        @if(count($presentaciones) > 0)
                            <div class="variant-row-header">
                                <div class="variant-col-label">Variante ej. 1L</div>
                                <div class="variant-col-label">Cód. Barras</div>
                                <div class="variant-col-label">Unid.</div>
                                <div class="variant-col-label">Costo S/</div>
                                <div class="variant-col-label">Precio S/</div>
                                <div class="variant-col-label">Stock Total</div>
                                <div></div>
                            </div>
                        @endif

                        @foreach($presentaciones as $index => $pres)
                            <div class="variant-row">
                                <div>
                                    <input type="text" class="form-control" wire:model="presentaciones.{{ $index }}.nombre_variante" placeholder="Ej: Botella 500ml">
                                    @error('presentaciones.'.$index.'.nombre_variante') <span class="form-error">Inválido</span> @enderror
                                </div>
                                <div>
                                    <input type="text" class="form-control" wire:model="presentaciones.{{ $index }}.codigo_barras" placeholder="123456789">
                                </div>
                                <div>
                                    <select class="form-control" wire:model="presentaciones.{{ $index }}.id_unidad">
                                        @foreach($unidades as $u)
                                            <option value="{{ $u->id_unidad }}">{{ $u->abreviatura }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <input type="number" step="0.01" class="form-control" wire:model="presentaciones.{{ $index }}.costo_reposicion" placeholder="0.00">
                                </div>
                                <div>
                                    <input type="number" step="0.01" class="form-control" wire:model="presentaciones.{{ $index }}.precio" placeholder="0.00">
                                    @error('presentaciones.'.$index.'.precio') <span class="form-error">Req</span> @enderror
                                </div>
                                <div>
                                    <input type="number" class="form-control" wire:model="presentaciones.{{ $index }}.stock" min="0" placeholder="0">
                                    @error('presentaciones.'.$index.'.stock') <span class="form-error">Req</span> @enderror
                                </div>
                                <button type="button" wire:click="removePresentacion({{ $index }})" class="btn-remove" title="Quitar variante">
                                    <i class="fas fa-trash"></i>
                                </button>
                                <div class="variant-image-tools">
                                    <div>
                                        <label class="form-label">Imagenes de esta presentación</label>
                                        <textarea class="form-control" wire:model="presentaciones.{{ $index }}.galeria_urls" rows="2" placeholder="Una URL por linea. La primera sera la principal."></textarea>
                                        <p class="variant-image-note">Si esta variante no tiene imagenes, la tienda usara la imagen general del producto.</p>
                                        @error('presentaciones.'.$index.'.galeria_urls') <span class="form-error">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="form-label">Subir imagenes</label>
                                        <input type="file" class="form-control" wire:model="presentaciones.{{ $index }}.imagenes_archivos" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" multiple>
                                        <p class="variant-image-note">Puedes subir varias fotos de la presentacion: frente, empaque, detalle o tamaño.</p>
                                        <div wire:loading wire:target="presentaciones.{{ $index }}.imagenes_archivos" class="variant-image-note">Cargando imagenes...</div>
                                        @error('presentaciones.'.$index.'.imagenes_archivos.*') <span class="form-error">{{ $message }}</span> @enderror
                                    </div>
                                    @php
                                        $savedVariantImages = $this->presentationImageUrls($index);
                                        $selectedVariantImages = $pres['imagenes_archivos'] ?? [];
                                        $selectedVariantImages = is_array($selectedVariantImages) ? $selectedVariantImages : [];
                                    @endphp
                                    @if(count($savedVariantImages) || count($selectedVariantImages))
                                        <div class="variant-image-preview-grid">
                                            @foreach($savedVariantImages as $imageIndex => $imageUrl)
                                                <div class="variant-image-preview" wire:key="variant-url-{{ $index }}-{{ md5($imageUrl) }}">
                                                    <div class="variant-image-preview-frame">
                                                        <img src="{{ $imageUrl }}" alt="Imagen guardada de {{ $pres['nombre_variante'] ?? 'presentacion' }}">
                                                        <button type="button" class="variant-image-preview-remove" wire:click.prevent="removePresentationUrl({{ $index }}, {{ $imageIndex }})" title="Eliminar imagen guardada">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </div>
                                                    <div class="variant-image-preview-label">{{ $imageIndex === 0 ? 'Principal' : 'Guardada' }}</div>
                                                </div>
                                            @endforeach
                                            @foreach($selectedVariantImages as $imageIndex => $imageFile)
                                                @php
                                                    $previewUrl = $this->temporaryUploadPreview($index, $imageIndex);
                                                @endphp
                                                <div class="variant-image-preview" wire:key="variant-upload-{{ $index }}-{{ $imageIndex }}">
                                                    <div class="variant-image-preview-frame">
                                                        @if($previewUrl)
                                                            <img src="{{ $previewUrl }}" alt="Nueva imagen de {{ $pres['nombre_variante'] ?? 'presentacion' }}">
                                                        @else
                                                            <span class="variant-photo-empty" style="width:100%;height:100%;border:none;border-radius:0;"><i class="fas fa-image"></i></span>
                                                        @endif
                                                        <button type="button" class="variant-image-preview-remove" wire:click.prevent="removePresentationUpload({{ $index }}, {{ $imageIndex }})" title="Quitar imagen seleccionada">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </div>
                                                    <div class="variant-image-preview-label">Nueva</div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div style="padding: 20px 24px; background: #ffffff; border-top: 1px solid var(--border); display: flex; justify-content: flex-end; gap: 12px; border-radius: 0 0 24px 24px;">
                    <button wire:click="$set('isModalOpen', false)" class="btn-ghost" style="padding: 12px 24px;">Cancelar</button>
                    <button wire:click="save" class="btn btn-primary" style="padding: 12px 32px; font-weight: 700; box-shadow: 0 8px 24px rgba(229,140,58,0.3);">
                        <i class="fas fa-save"></i> Procesar y Guardar
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
