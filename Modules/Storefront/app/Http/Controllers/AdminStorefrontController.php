<?php

namespace Modules\Storefront\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Support\SafeImageUpload;
use Illuminate\Http\Request;
use Modules\Storefront\Models\StorefrontSetting;

class AdminStorefrontController extends Controller
{
    public function index()
    {
        return view('storefront::admin.storefront', [
            'setting' => StorefrontSetting::current(),
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'store_name' => 'required|string|max:80',
            'store_tagline' => 'nullable|string|max:120',
            'logo_url' => 'nullable|string|max:255',
            'logo_archivo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'remove_logo' => 'nullable|boolean',
            'primary_color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'primary_light_color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'primary_dark_color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'accent_color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'header_style' => 'required|in:solid,dark',
            'card_style' => 'required|in:rounded,compact,flat',
            'show_login_link' => 'nullable|boolean',
            'footer_text' => 'nullable|string|max:160',
        ], [
            'primary_color.regex' => 'El color principal debe estar en formato hexadecimal, por ejemplo #f97316.',
            'primary_light_color.regex' => 'El color claro debe estar en formato hexadecimal, por ejemplo #fb923c.',
            'primary_dark_color.regex' => 'El color oscuro debe estar en formato hexadecimal, por ejemplo #ea580c.',
            'accent_color.regex' => 'El color de contraste debe estar en formato hexadecimal, por ejemplo #1f2937.',
        ]);

        $setting = StorefrontSetting::current();
        $logoUrl = $setting->logo_url;

        if ($request->boolean('remove_logo')) {
            $logoUrl = null;
        }

        if (!empty($data['logo_url'])) {
            $logoUrl = StorefrontSetting::normalizeLogoUrl($data['logo_url']);
        }

        if ($request->hasFile('logo_archivo')) {
            $storedLogoUrl = SafeImageUpload::store($request->file('logo_archivo'), 'images/storefront', 'logo');
            $logoUrl = StorefrontSetting::normalizeLogoUrl(
                parse_url($storedLogoUrl, PHP_URL_PATH) ?: $storedLogoUrl
            );
        }

        $setting->fill([
            'store_name' => $data['store_name'],
            'store_tagline' => $data['store_tagline'] ?? '',
            'logo_url' => $logoUrl,
            'primary_color' => strtolower($data['primary_color']),
            'primary_light_color' => strtolower($data['primary_light_color']),
            'primary_dark_color' => strtolower($data['primary_dark_color']),
            'accent_color' => strtolower($data['accent_color']),
            'header_style' => $data['header_style'],
            'card_style' => $data['card_style'],
            'show_login_link' => $request->boolean('show_login_link'),
            'footer_text' => $data['footer_text'] ?? null,
        ])->save();

        return back()->with('success', 'Storefront actualizado correctamente.');
    }
}
