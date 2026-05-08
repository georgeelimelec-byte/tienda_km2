@extends('layouts.admin')

@section('title', 'Storefront')
@section('page-title', 'Personalizar Storefront')
@section('page-kicker', 'Identidad y diseño de la tienda virtual')

@section('topbar-actions')
    <a href="{{ route('admin.configuracion.index') }}" class="topbar-badge" style="text-decoration:none;">
        <i class="fas fa-gear"></i> Configuracion
    </a>
    <a href="{{ route('storefront.index') }}" target="_blank" rel="noopener" class="topbar-badge" style="text-decoration:none;">
        <i class="fas fa-arrow-up-right-from-square"></i> Ver tienda
    </a>
@endsection

@push('styles')
<style>
    .storefront-config {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 380px;
        gap: 24px;
        align-items: start;
    }

    .config-card {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        box-shadow: 0 18px 45px -34px rgba(15, 23, 42, 0.45);
        overflow: hidden;
    }

    .config-card-header {
        padding: 22px 24px;
        border-bottom: 1px solid #edf0f3;
    }

    .config-card-title {
        margin: 0;
        color: #0f172a;
        font-size: 18px;
        font-weight: 900;
    }

    .config-card-subtitle {
        margin: 6px 0 0;
        color: #64748b;
        font-size: 14px;
        line-height: 1.5;
    }

    .config-body {
        padding: 24px;
        display: grid;
        gap: 20px;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 16px;
    }

    .form-full {
        grid-column: 1 / -1;
    }

    .form-label {
        display: block;
        margin-bottom: 7px;
        color: #334155;
        font-size: 12px;
        font-weight: 900;
        letter-spacing: 0.02em;
    }

    .form-control {
        width: 100%;
        min-height: 46px;
        border: 1px solid #dbe1e8;
        border-radius: 10px;
        background: #f8fafc;
        color: #0f172a;
        padding: 10px 13px;
        font-size: 14px;
        font-family: inherit;
        outline: none;
        transition: border-color 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
    }

    .form-control:focus {
        background: #ffffff;
        border-color: var(--primary);
        box-shadow: 0 0 0 4px rgba(229, 140, 58, 0.12);
    }

    .color-row {
        display: grid;
        grid-template-columns: 54px minmax(0, 1fr);
        gap: 10px;
        align-items: center;
    }

    .color-row input[type="color"] {
        width: 54px;
        height: 46px;
        padding: 4px;
        border-radius: 10px;
        border: 1px solid #dbe1e8;
        background: #ffffff;
    }

    .toggle-line {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 13px 14px;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        background: #f8fafc;
        color: #334155;
        font-weight: 800;
    }

    .logo-preview {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 14px;
        border: 1px dashed #dbe1e8;
        border-radius: 12px;
        background: #f8fafc;
    }

    .logo-preview-frame {
        width: 82px;
        height: 82px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        background: #ffffff;
        border: 1px solid #e5e7eb;
        overflow: hidden;
    }

    .logo-preview-frame img {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }

    .preview-shell {
        overflow: hidden;
    }

    .preview-header {
        min-height: 82px;
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 16px;
        color: #ffffff;
    }

    .preview-logo {
        width: 58px;
        height: 58px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        background: rgba(255,255,255,0.92);
        overflow: hidden;
        color: #0f172a;
        font-weight: 900;
    }

    .preview-logo img {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }

    .preview-body {
        padding: 18px;
        background: #f8fafc;
    }

    .preview-product {
        border: 1px solid #e5e7eb;
        background: #ffffff;
        padding: 14px;
        box-shadow: 0 16px 30px -26px rgba(15, 23, 42, 0.45);
    }

    .preview-product.rounded { border-radius: 18px; }
    .preview-product.compact { border-radius: 10px; padding: 10px; }
    .preview-product.flat { border-radius: 6px; box-shadow: none; }

    .preview-image {
        height: 90px;
        border-radius: 10px;
        background: linear-gradient(135deg, #fff7ed, #fed7aa);
        margin-bottom: 12px;
    }

    .config-actions {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        padding: 18px 24px;
        border-top: 1px solid #edf0f3;
        background: #ffffff;
    }

    @media (max-width: 1120px) {
        .storefront-config {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 720px) {
        .form-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
    <div class="storefront-config">
        <form method="POST" action="{{ route('admin.storefront.update') }}" enctype="multipart/form-data" class="config-card">
            @csrf
            <div class="config-card-header">
                <h2 class="config-card-title">Apariencia de la tienda</h2>
                <p class="config-card-subtitle">Estos valores se aplican al header, nombre comercial, logo, colores principales, enlaces visibles y tarjetas de productos.</p>
            </div>

            <div class="config-body">
                @if($errors->any())
                    <div style="border-left: 4px solid #ef4444; background: #fef2f2; color: #991b1b; padding: 14px 16px; border-radius: 10px;">
                        <strong>Revisa la configuración:</strong>
                        <ul style="margin: 8px 0 0; padding-left: 18px;">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="form-grid">
                    <div>
                        <label class="form-label" for="store_name">Nombre de tienda</label>
                        <input id="store_name" name="store_name" class="form-control" value="{{ old('store_name', $setting->store_name) }}" maxlength="80" required>
                    </div>
                    <div>
                        <label class="form-label" for="store_tagline">Subtitulo</label>
                        <input id="store_tagline" name="store_tagline" class="form-control" value="{{ old('store_tagline', $setting->store_tagline) }}" maxlength="120">
                    </div>

                    <div class="form-full">
                        @php
                            $customLogo = old('logo_url', $setting->logo_url);
                            $logo = $customLogo
                                ? \Modules\Storefront\Models\StorefrontSetting::normalizeLogoUrl($customLogo)
                                : $setting->displayLogoUrl();
                        @endphp
                        <label class="form-label">Logo actual</label>
                        <div class="logo-preview">
                            <div class="logo-preview-frame">
                                @if($logo)
                                    <img src="{{ $logo }}" alt="Logo actual" id="logo-preview-image">
                                @else
                                    <strong id="logo-preview-text">{{ substr($setting->store_name ?: 'KM2', 0, 3) }}</strong>
                                @endif
                            </div>
                            <div style="min-width:0;">
                                <div style="font-weight:900;color:#0f172a;">{{ $setting->store_name }}</div>
                                <div style="color:#64748b;font-size:13px;margin-top:3px;">Sube un archivo para procesarlo con protección o pega una URL pública.</div>
                            </div>
                        </div>
                    </div>

                    <div class="form-full">
                        <label class="form-label" for="logo_url">URL de logo</label>
                        <input id="logo_url" name="logo_url" class="form-control" value="{{ old('logo_url', $setting->logo_url) }}" placeholder="https://...">
                    </div>

                    <div class="form-full">
                        <label class="form-label" for="logo_archivo">Subir logo</label>
                        <input id="logo_archivo" name="logo_archivo" type="file" class="form-control" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
                        <p style="margin:7px 0 0;color:#64748b;font-size:12px;">Se validará como imagen real y se convertirá a WebP antes de quedar pública.</p>
                    </div>

                    <label class="toggle-line form-full">
                        <input type="checkbox" name="remove_logo" value="1">
                        Quitar logo personalizado y usar el logo por defecto del sistema
                    </label>
                </div>

                <div class="form-grid">
                    <div>
                        <label class="form-label" for="primary_color">Color principal</label>
                        <div class="color-row">
                            <input type="color" id="primary_color_picker" value="{{ old('primary_color', $setting->primary_color) }}">
                            <input id="primary_color" name="primary_color" class="form-control" value="{{ old('primary_color', $setting->primary_color) }}" required>
                        </div>
                    </div>
                    <div>
                        <label class="form-label" for="primary_dark_color">Color oscuro</label>
                        <div class="color-row">
                            <input type="color" id="primary_dark_color_picker" value="{{ old('primary_dark_color', $setting->primary_dark_color) }}">
                            <input id="primary_dark_color" name="primary_dark_color" class="form-control" value="{{ old('primary_dark_color', $setting->primary_dark_color) }}" required>
                        </div>
                    </div>
                    <div>
                        <label class="form-label" for="primary_light_color">Color claro</label>
                        <div class="color-row">
                            <input type="color" id="primary_light_color_picker" value="{{ old('primary_light_color', $setting->primary_light_color) }}">
                            <input id="primary_light_color" name="primary_light_color" class="form-control" value="{{ old('primary_light_color', $setting->primary_light_color) }}" required>
                        </div>
                    </div>
                    <div>
                        <label class="form-label" for="accent_color">Color de contraste</label>
                        <div class="color-row">
                            <input type="color" id="accent_color_picker" value="{{ old('accent_color', $setting->accent_color) }}">
                            <input id="accent_color" name="accent_color" class="form-control" value="{{ old('accent_color', $setting->accent_color) }}" required>
                        </div>
                    </div>
                </div>

                <div class="form-grid">
                    <div>
                        <label class="form-label" for="header_style">Diseño de header</label>
                        <select id="header_style" name="header_style" class="form-control">
                            <option value="solid" @selected(old('header_style', $setting->header_style) === 'solid')>Color sólido de marca</option>
                            <option value="dark" @selected(old('header_style', $setting->header_style) === 'dark')>Oscuro premium</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label" for="card_style">Diseño de tarjetas</label>
                        <select id="card_style" name="card_style" class="form-control">
                            <option value="rounded" @selected(old('card_style', $setting->card_style) === 'rounded')>Redondeadas</option>
                            <option value="compact" @selected(old('card_style', $setting->card_style) === 'compact')>Compactas</option>
                            <option value="flat" @selected(old('card_style', $setting->card_style) === 'flat')>Planas</option>
                        </select>
                    </div>
                    <input type="hidden" name="show_login_link" value="0">
                    <label class="toggle-line">
                        <input type="checkbox" name="show_login_link" value="1" @checked(old('show_login_link', $setting->show_login_link))>
                        Mostrar acceso de usuario/admin en tienda
                    </label>
                    <div>
                        <label class="form-label" for="footer_text">Texto de pie de página</label>
                        <input id="footer_text" name="footer_text" class="form-control" value="{{ old('footer_text', $setting->footer_text) }}" maxlength="160" placeholder="Market KM2. Minimarket y cafeteria.">
                    </div>
                </div>
            </div>

            <div class="config-actions">
                <a href="{{ route('storefront.index') }}" target="_blank" rel="noopener" class="btn-ghost" style="padding: 12px 18px; text-decoration:none;">Vista pública</a>
                <button type="submit" class="btn btn-primary" style="padding: 12px 24px; font-weight: 900;">
                    <i class="fas fa-save"></i> Guardar cambios
                </button>
            </div>
        </form>

        <aside class="config-card preview-shell">
            <div class="preview-header" id="preview-header" style="background: {{ $setting->header_style === 'dark' ? $setting->accent_color : $setting->primary_color }};">
                <div class="preview-logo">
                    @if($setting->displayLogoUrl())
                        <img src="{{ $setting->displayLogoUrl() }}" alt="{{ $setting->store_name }}">
                    @else
                        KM2
                    @endif
                </div>
                <div>
                    <div id="preview-name" style="font-size:20px;font-weight:900;line-height:1;">{{ $setting->store_name }}</div>
                    <div id="preview-tagline" style="font-size:12px;font-weight:800;opacity:.82;margin-top:4px;">{{ $setting->store_tagline }}</div>
                </div>
            </div>
            <div class="preview-body">
                <div class="preview-product {{ $setting->card_style }}" id="preview-card">
                    <div class="preview-image"></div>
                    <div style="font-size:12px;font-weight:900;color:{{ $setting->primary_color }};">Categoria</div>
                    <div style="margin-top:4px;font-size:18px;font-weight:900;color:#0f172a;">Producto de muestra</div>
                    <div style="margin-top:8px;font-size:20px;font-weight:900;color:{{ $setting->primary_color }};">S/ 12.90</div>
                </div>
            </div>
        </aside>
    </div>

    <script>
        document.querySelectorAll('input[type="color"]').forEach((picker) => {
            const target = document.getElementById(picker.id.replace('_picker', ''));
            if (!target) return;
            picker.addEventListener('input', () => {
                target.value = picker.value;
                updatePreview();
            });
            target.addEventListener('input', () => {
                if (/^#[0-9A-Fa-f]{6}$/.test(target.value)) {
                    picker.value = target.value;
                    updatePreview();
                }
            });
        });

        ['store_name', 'store_tagline', 'header_style', 'card_style'].forEach((id) => {
            const element = document.getElementById(id);
            if (element) element.addEventListener('input', updatePreview);
        });

        const logoInput = document.getElementById('logo_archivo');
        if (logoInput) {
            logoInput.addEventListener('change', (event) => {
                const file = event.target.files && event.target.files[0];
                if (!file) return;
                const reader = new FileReader();
                reader.onload = () => {
                    const frame = document.querySelector('.preview-logo');
                    const current = document.getElementById('logo-preview-image');
                    frame.innerHTML = `<img src="${reader.result}" alt="Logo seleccionado">`;
                    if (current) current.src = reader.result;
                };
                reader.readAsDataURL(file);
            });
        }

        function updatePreview() {
            const name = document.getElementById('store_name')?.value || 'Market KM2';
            const tagline = document.getElementById('store_tagline')?.value || 'Minimarket & Cafe';
            const primary = document.getElementById('primary_color')?.value || '#f97316';
            const accent = document.getElementById('accent_color')?.value || '#1f2937';
            const headerStyle = document.getElementById('header_style')?.value || 'solid';
            const cardStyle = document.getElementById('card_style')?.value || 'rounded';

            document.getElementById('preview-name').textContent = name;
            document.getElementById('preview-tagline').textContent = tagline;
            document.getElementById('preview-header').style.background = headerStyle === 'dark' ? accent : primary;
            document.querySelectorAll('.preview-product [style*="color"]').forEach((element) => {
                if (element.textContent.includes('Categoria') || element.textContent.includes('S/')) {
                    element.style.color = primary;
                }
            });

            const card = document.getElementById('preview-card');
            card.classList.remove('rounded', 'compact', 'flat');
            card.classList.add(cardStyle);
        }
    </script>
@endsection
