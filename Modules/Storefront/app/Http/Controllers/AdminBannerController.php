<?php

namespace Modules\Storefront\Http\Controllers;

use App\Support\SafeImageUpload;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Storefront\Models\BannerWeb;

class AdminBannerController extends Controller
{
    public function index()
    {
        $banners = BannerWeb::orderByRaw("CASE WHEN estado = 'Activo' THEN 0 ELSE 1 END")
            ->orderByDesc('id_banner')
            ->get();

        return view('storefront::admin.banners', compact('banners'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'titulo' => 'required|string|max:100',
            'imagen_url' => 'nullable|url|max:255',
            'imagen_archivo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'link_destino' => 'nullable|string|max:255',
            'posicion' => 'nullable|in:Carrusel,Lateral,Pop_up',
            'estado' => 'required|in:Activo,Inactivo',
        ]);

        $imageUrl = $this->resolveImageUrl($request, $data['imagen_url'] ?? null);
        if (!$imageUrl) {
            return back()->withErrors(['imagen_url' => 'Agrega una URL de imagen o sube un archivo.']);
        }

        BannerWeb::create([
            'titulo' => $data['titulo'],
            'imagen_url' => $imageUrl,
            'link_destino' => $data['link_destino'] ?? '/',
            'posicion' => $data['posicion'] ?? 'Carrusel',
            'estado' => $data['estado'],
        ]);

        return back()->with('success', 'Banner creado correctamente');
    }

    public function update(Request $request, int $id)
    {
        $banner = BannerWeb::findOrFail($id);

        $data = $request->validate([
            'titulo' => 'required|string|max:100',
            'imagen_url' => 'nullable|url|max:255',
            'imagen_archivo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'link_destino' => 'nullable|string|max:255',
            'posicion' => 'nullable|in:Carrusel,Lateral,Pop_up',
            'estado' => 'required|in:Activo,Inactivo',
        ]);

        $imageUrl = $this->resolveImageUrl($request, $data['imagen_url'] ?? null) ?: $banner->imagen_url;

        $banner->update([
            'titulo' => $data['titulo'],
            'imagen_url' => $imageUrl,
            'link_destino' => $data['link_destino'] ?? '/',
            'posicion' => $data['posicion'] ?? 'Carrusel',
            'estado' => $data['estado'],
        ]);

        return back()->with('success', 'Banner actualizado correctamente');
    }

    public function destroy(int $id)
    {
        BannerWeb::findOrFail($id)->delete();

        return back()->with('success', 'Banner eliminado correctamente');
    }

    private function resolveImageUrl(Request $request, ?string $url): ?string
    {
        if ($request->hasFile('imagen_archivo')) {
            return SafeImageUpload::store($request->file('imagen_archivo'), 'images/banners', 'banner');
        }

        return $url ?: null;
    }
}
