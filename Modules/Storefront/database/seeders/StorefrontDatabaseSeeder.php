<?php

namespace Modules\Storefront\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Modules\Storefront\Models\BannerWeb;
use Modules\Storefront\Models\Cliente;
use Modules\Storefront\Models\Promocion;
use Modules\Storefront\Models\StorefrontSetting;
use Modules\Storefront\Models\ZonaDelivery;

class StorefrontDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->seedStorefrontSettings();
        $this->seedCustomers();
        $this->seedDeliveryZones();
        $this->seedBanners();
        $this->seedPromotions();
    }

    private function seedStorefrontSettings(): void
    {
        StorefrontSetting::updateOrCreate(
            ['id' => 1],
            array_merge(StorefrontSetting::defaults(), [
                'whatsapp_number' => '51999999999',
                'contact_phone' => '999999999',
                'contact_email' => 'ventas@marketkm2.test',
                'currency' => 'PEN',
                'included_tax_percent' => 18.00,
                'business_hours' => 'Lunes a domingo',
                'control_stock_habilitado' => true,
            ])
        );
    }

    private function seedCustomers(): void
    {
        foreach ([
            [
                'id_cliente' => 1,
                'nombre_o_razon_social' => 'Maria Torres',
                'email' => 'maria@example.test',
                'celular' => '51911111111',
                'direccion' => 'Av. Principal 120',
            ],
            [
                'id_cliente' => 2,
                'nombre_o_razon_social' => 'Luis Ramirez',
                'email' => 'luis@example.test',
                'celular' => '51922222222',
                'direccion' => 'Jr. Los Cedros 450',
            ],
            [
                'id_cliente' => 3,
                'nombre_o_razon_social' => 'Carla Mendoza',
                'email' => 'carla@example.test',
                'celular' => '51933333333',
                'direccion' => 'Calle Comercio 245',
            ],
        ] as $customer) {
            Cliente::updateOrCreate(
                ['id_cliente' => $customer['id_cliente']],
                $customer + [
                    'tipo_documento' => 'Sin Documento',
                    'password' => Hash::make('cliente123'),
                ]
            );
        }
    }

    private function seedDeliveryZones(): void
    {
        foreach ([
            ['id_zona' => 1, 'nombre' => 'Recojo en tienda', 'tarifa' => 0.00, 'estado' => 'Activo'],
            ['id_zona' => 2, 'nombre' => 'Cercado de Lima', 'tarifa' => 8.00, 'estado' => 'Activo'],
            ['id_zona' => 3, 'nombre' => 'San Isidro', 'tarifa' => 10.00, 'estado' => 'Activo'],
            ['id_zona' => 4, 'nombre' => 'Miraflores', 'tarifa' => 12.00, 'estado' => 'Activo'],
            ['id_zona' => 5, 'nombre' => 'Surco', 'tarifa' => 14.00, 'estado' => 'Activo'],
        ] as $zone) {
            ZonaDelivery::updateOrCreate(
                ['id_zona' => $zone['id_zona']],
                $zone
            );
        }
    }

    private function seedBanners(): void
    {
        foreach ([
            [
                'id_banner' => 1,
                'titulo' => 'Cafe al paso y panaderia fresca',
                'imagen_url' => 'https://images.unsplash.com/photo-1554118811-1e0d58224f24?auto=format&fit=crop&q=80&w=1800',
                'link_destino' => '/?categoria_id=5',
            ],
            [
                'id_banner' => 2,
                'titulo' => 'Promociones de despensa KM2',
                'imagen_url' => 'https://images.unsplash.com/photo-1604719312566-8912e9227c6a?auto=format&fit=crop&q=80&w=1800',
                'link_destino' => '/?categoria_id=2',
            ],
            [
                'id_banner' => 3,
                'titulo' => 'Bebidas frias, snacks y recojo rapido',
                'imagen_url' => 'https://images.unsplash.com/photo-1621939514649-280e2ee25f60?auto=format&fit=crop&q=80&w=1800',
                'link_destino' => '/?categoria_id=3',
            ],
            [
                'id_banner' => 4,
                'titulo' => 'Combos de cafeteria para la oficina',
                'imagen_url' => 'https://images.unsplash.com/photo-1528735602780-2552fd46c7af?auto=format&fit=crop&q=80&w=1800',
                'link_destino' => '/?categoria_id=6',
            ],
        ] as $banner) {
            BannerWeb::updateOrCreate(
                ['id_banner' => $banner['id_banner']],
                $banner + [
                    'posicion' => 'Carrusel',
                    'estado' => 'Activo',
                ]
            );
        }
    }

    private function seedPromotions(): void
    {
        $promo = Promocion::updateOrCreate(
            ['nombre' => 'Promocion cafeteria KM2'],
            [
                'descripcion' => 'Descuento piloto para productos de cafeteria publicados en la tienda virtual.',
                'tipo_descuento' => 'Porcentaje',
                'valor_descuento' => 10,
                'fecha_inicio' => now()->subDay()->toDateString(),
                'fecha_fin' => now()->addWeeks(4)->toDateString(),
                'estado' => 'Activo',
            ]
        );

        $promo->categorias()->sync([5, 6, 7]);
    }

}
