<?php

namespace Modules\Storefront\Models;

use Illuminate\Database\Eloquent\Model;

class StorefrontSetting extends Model
{
    protected $table = 'configuracion_tienda';

    protected $fillable = [
        'store_name',
        'store_tagline',
        'logo_url',
        'primary_color',
        'primary_light_color',
        'primary_dark_color',
        'accent_color',
        'header_style',
        'card_style',
        'show_login_link',
        'footer_text',
        'whatsapp_number',
        'contact_phone',
        'contact_email',
        'currency',
        'included_tax_percent',
        'business_hours',
        'operational_message',
        'control_stock_habilitado',
    ];

    protected $casts = [
        'show_login_link' => 'boolean',
        'included_tax_percent' => 'decimal:2',
        'control_stock_habilitado' => 'boolean',
    ];

    public static function defaults(): array
    {
        return [
            'store_name' => 'Market KM2',
            'store_tagline' => 'Minimarket & Cafe',
            'logo_url' => null,
            'primary_color' => '#f97316',
            'primary_light_color' => '#fb923c',
            'primary_dark_color' => '#ea580c',
            'accent_color' => '#1f2937',
            'header_style' => 'solid',
            'card_style' => 'rounded',
            'show_login_link' => true,
            'footer_text' => null,
            'whatsapp_number' => '51999999999',
            'contact_phone' => null,
            'contact_email' => 'ventas@marketkm2.test',
            'currency' => 'PEN',
            'included_tax_percent' => 18.00,
            'business_hours' => 'Lunes a domingo',
            'operational_message' => null,
            'control_stock_habilitado' => true,
        ];
    }

    public static function current(): self
    {
        try {
            return self::query()->firstOrCreate(['id' => 1], self::defaults());
        } catch (\Throwable $e) {
            return new self(self::defaults());
        }
    }

    public function displayLogoUrl(): ?string
    {
        $logoUrl = self::normalizeLogoUrl($this->logo_url);

        if ($logoUrl && ! preg_match('/^https?:\/\//i', $logoUrl) && ! str_starts_with($logoUrl, 'data:')) {
            $relativePath = ltrim(parse_url($logoUrl, PHP_URL_PATH) ?: $logoUrl, '/');

            if (! file_exists(public_path($relativePath))) {
                $logoUrl = null;
            }
        }

        return $logoUrl ?: self::defaultLogoUrl();
    }

    public static function normalizeLogoUrl(?string $url): ?string
    {
        $url = trim((string) $url);

        if ($url === '') {
            return null;
        }

        if (preg_match('/^https?:\/\//i', $url) || str_starts_with($url, 'data:')) {
            return $url;
        }

        $path = parse_url($url, PHP_URL_PATH) ?: $url;

        return '/' . ltrim($path, '/');
    }

    public static function defaultLogoUrl(): ?string
    {
        $path = collect([
            'images/logo-marketkm2.webp',
            'images/logomarket.webp',
            'images/company-logo.svg',
            'images/company-logo.png',
            'images/company-logo.webp',
            'images/company-logo.jpg',
            'images/company-logo.jpeg',
        ])->first(fn ($path) => file_exists(public_path($path)));

        return $path ? '/' . $path : null;
    }

    public function whatsappNumberForUrl(): string
    {
        return preg_replace('/\D+/', '', (string) $this->whatsapp_number) ?: '51999999999';
    }

    public function stockControlEnabled(): bool
    {
        return (bool) ($this->control_stock_habilitado ?? true);
    }
}
