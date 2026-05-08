<div>
    <style>
        .page-toolbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
        
        .cat-card { background: #ffffff; border: 1px solid var(--border); border-radius: 16px; margin-bottom: 16px; box-shadow: var(--shadow); transition: var(--transition); overflow: hidden; }
        .cat-card:hover { box-shadow: var(--shadow-lg); border-color: rgba(229,140,58,0.2); }
        
        .cat-row { padding: 16px 20px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border); }
        .cat-row:last-child { border-bottom: none; }
        .cat-row.root { background: #fdfcfb; border-bottom: 2px solid var(--border); }
        
        .cat-info { display: flex; align-items: center; gap: 12px; }
        .cat-title { font-size: 16px; font-weight: 800; color: var(--text-primary); }
        .cat-title.sub { font-size: 14px; font-weight: 600; color: var(--text-secondary); }
        
        .cat-tree-line { width: 24px; height: 24px; border-left: 2px solid #e2d9d1; border-bottom: 2px solid #e2d9d1; border-bottom-left-radius: 8px; margin-right: 8px; display: inline-block; margin-top: -12px; }
        
        .sub-container { padding-left: 40px; background: #ffffff; }
        .sub-sub-container { padding-left: 40px; background: #ffffff; }

        .btn-ghost { padding: 6px 10px; border-radius: 8px; background: rgba(0,0,0,0.03); color: var(--text-secondary); font-size: 13px; border: none; cursor: pointer; transition: all 0.2s; }
        .btn-ghost:hover { background: rgba(229,140,58,0.1); color: var(--primary); }
        .btn-danger { background: rgba(220,38,38,0.05); color: #dc2626; }
        .btn-danger:hover { background: rgba(220,38,38,0.15); color: #b91c1c; }

        .form-label { display: block; font-size: 12px; font-weight: 700; color: var(--text-secondary); margin-bottom: 6px; }
        .form-control { width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: 10px; font-size: 14px; background: #ffffff; transition: all 0.2s; margin-bottom: 16px; }
        .form-control:focus { border-color: var(--primary); outline: none; box-shadow: 0 0 0 3px rgba(229,140,58,0.1); }
        .form-error { font-size: 12px; color: #dc2626; display: block; margin-top: -12px; margin-bottom: 16px; }
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

    <div class="page-toolbar">
        <div>
            <h3 style="margin:0; font-size: 18px; font-weight: 700; color: var(--text-primary);">Tus Categorías</h3>
            <p style="margin:4px 0 0; font-size: 13px; color: var(--text-muted);">Organiza tu inventario en grupos y subgrupos lógicos.</p>
        </div>
        <button wire:click="create" class="btn btn-primary" style="padding: 12px 24px;">
            <i class="fas fa-plus"></i> Añadir Categoría
        </button>
    </div>

    <!-- Tree View -->
    <div>
        @forelse($categoriasTree as $rootCat)
            <div class="cat-card">
                <!-- Root Level -->
                <div class="cat-row root">
                    <div class="cat-info">
                        <i class="fas fa-folder text-primary" style="font-size: 18px;"></i>
                        <span class="cat-title">{{ $rootCat->nombre }}</span>
                    </div>
                    <div style="display: flex; gap: 8px;">
                        <button wire:click="create({{ $rootCat->id_categoria }})" class="btn-ghost" title="Añadir subcategoría">
                            <i class="fas fa-code-branch"></i> Sub
                        </button>
                        <button wire:click="edit({{ $rootCat->id_categoria }})" class="btn-ghost">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button onclick="confirm('¿Estás seguro de eliminar {{ $rootCat->nombre }}?') || event.stopImmediatePropagation()" wire:click="delete({{ $rootCat->id_categoria }})" class="btn-ghost btn-danger">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>

                <!-- L2: Hijos -->
                @if($rootCat->hijos->count() > 0)
                    <div class="sub-container">
                        @foreach($rootCat->hijos as $subCat)
                            <div class="cat-row">
                                <div class="cat-info">
                                    <div class="cat-tree-line"></div>
                                    <i class="fas fa-folder-open text-muted" style="font-size: 14px;"></i>
                                    <span class="cat-title sub">{{ $subCat->nombre }}</span>
                                </div>
                                <div style="display: flex; gap: 8px;">
                                    <button wire:click="create({{ $subCat->id_categoria }})" class="btn-ghost" title="Añadir sub-subcategoría">
                                        <i class="fas fa-code-branch"></i>
                                    </button>
                                    <button wire:click="edit({{ $subCat->id_categoria }})" class="btn-ghost">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button onclick="confirm('¿Estás seguro de eliminar {{ $subCat->nombre }}?') || event.stopImmediatePropagation()" wire:click="delete({{ $subCat->id_categoria }})" class="btn-ghost btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- L3: Nietos -->
                            @if($subCat->hijos->count() > 0)
                                <div class="sub-sub-container">
                                    @foreach($subCat->hijos as $grandCat)
                                        <div class="cat-row">
                                            <div class="cat-info">
                                                <div class="cat-tree-line" style="border-left: 2px dotted #d1d5db; border-bottom: 2px dotted #d1d5db;"></div>
                                                <i class="fas fa-tag text-muted" style="font-size: 13px;"></i>
                                                <span class="cat-title sub" style="font-weight: 500;">{{ $grandCat->nombre }}</span>
                                            </div>
                                            <div style="display: flex; gap: 8px;">
                                                <button wire:click="edit({{ $grandCat->id_categoria }})" class="btn-ghost">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button onclick="confirm('¿Estás seguro de eliminar {{ $grandCat->nombre }}?') || event.stopImmediatePropagation()" wire:click="delete({{ $grandCat->id_categoria }})" class="btn-ghost btn-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        @endforeach
                    </div>
                @endif
            </div>
        @empty
            <div style="text-align: center; padding: 60px; background: #ffffff; border-radius: 16px; border: 1px dashed var(--border);">
                <div style="font-size: 48px; color: var(--border); margin-bottom: 16px;"><i class="fas fa-tags"></i></div>
                <h3 style="font-size: 18px; color: var(--text-primary); margin-bottom: 8px;">Sin Categorías</h3>
                <p style="color: var(--text-muted); margin-bottom: 24px;">No has creado agrupaciones para tu catálogo.</p>
                <button wire:click="create" class="btn btn-primary" style="padding: 10px 20px;">Crear la Primera</button>
            </div>
        @endforelse
    </div>

    <!-- MODAL -->
    @if($isModalOpen)
        <div class="admin-panel-modal-overlay is-open"></div>
        <div class="admin-panel-modal is-open" aria-hidden="false" style="padding: 24px;">
            <div class="admin-panel-modal-card" style="max-width: 450px; transform: scale(1); animation: modalFadeIn 0.2s ease-out;">
                <style> @keyframes modalFadeIn { from{ opacity: 0; transform: translateY(20px); } to { opacity:1; transform: translateY(0); } } </style>
                
                <div class="admin-panel-modal-header" style="background: linear-gradient(135deg, #e58c3a, #f7b752); border-bottom: none;">
                    <div>
                        <div class="admin-panel-modal-title" style="color:#ffffff;">{{ $categoryId ? 'Editar Categoría' : 'Nueva Categoría' }}</div>
                    </div>
                    <button wire:click="$set('isModalOpen', false)" class="admin-panel-modal-close" style="background: rgba(255,255,255,0.2); border: none; color: white;">
                        <i class="fas fa-xmark"></i>
                    </button>
                </div>
                
                <div style="padding: 24px; background:#fff;">
                    <label class="form-label">Nombre de Categoría</label>
                    <input type="text" class="form-control" wire:model="nombre" placeholder="Ej: Lácteos">
                    @error('nombre') <span class="form-error">{{ $message }}</span> @enderror

                    <label class="form-label">Subcategoría de (Opcional)</label>
                    <select class="form-control" wire:model="id_categoria_padre">
                        <option value="">-- Es una Categoría Principal --</option>
                        @foreach($todasCategorias as $cat)
                            <option value="{{ $cat->id_categoria }}">{{ $cat->nombre }}</option>
                        @endforeach
                    </select>
                    @error('id_categoria_padre') <span class="form-error">{{ $message }}</span> @enderror

                    <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 16px;">
                        <button wire:click="$set('isModalOpen', false)" class="btn-ghost" style="padding: 10px 20px;">Cancelar</button>
                        <button wire:click="save" class="btn btn-primary" style="padding: 10px 24px;">
                            <i class="fas fa-save"></i> Guardar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
