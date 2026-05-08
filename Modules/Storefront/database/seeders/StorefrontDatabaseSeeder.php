<?php

namespace Modules\Storefront\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Modules\Inventory\Models\Producto;
use Modules\Storefront\Models\BannerWeb;
use Modules\Storefront\Models\Cliente;
use Modules\Storefront\Models\Resena;
use Modules\Storefront\Models\ZonaDelivery;

class StorefrontDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->seedCompany();
        $this->seedDeliveryZones();
        $this->seedBanners();
        $this->seedReviews();
    }

    private function seedCompany(): void
    {
        DB::table('empresa_configuracion')->updateOrInsert(
            ['id_empresa' => 1],
            [
                'ruc' => '20600000001',
                'razon_social' => 'Market KM2 S.A.C.',
                'nombre_comercial' => 'Market KM2',
                'logo_url' => 'logo_default.png',
                'direccion_fiscal' => 'Lima, Peru',
                'telefono_contacto' => '999999999',
                'correo_contacto' => 'ventas@marketkm2.test',
                'ubigeo' => '150101',
                'porcentaje_igv' => 18.00,
                'estado' => 'Activo',
            ]
        );
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

    private function seedReviews(): void
    {
        $clients = [
            ['celular' => '51911111111', 'nombre_o_razon_social' => 'Maria Torres', 'email' => 'maria@example.test'],
            ['celular' => '51922222222', 'nombre_o_razon_social' => 'Luis Ramirez', 'email' => 'luis@example.test'],
            ['celular' => '51933333333', 'nombre_o_razon_social' => 'Carla Mendoza', 'email' => 'carla@example.test'],
        ];

        $clientModels = collect($clients)->map(fn ($client) => Cliente::updateOrCreate(
            ['celular' => $client['celular']],
            $client + [
                'tipo_documento' => 'Sin Documento',
                'password' => Hash::make('cliente123'),
            ]
        ));

        foreach (Producto::where('estado', 'Activo')->take(5)->get() as $index => $product) {
            $client = $clientModels[$index % $clientModels->count()];
            Resena::updateOrCreate(
                ['id_producto' => $product->id_producto, 'id_cliente' => $client->id_cliente],
                [
                    'calificacion' => [5, 4, 5, 4, 5][$index] ?? 5,
                    'comentario' => [
                        'Cafe fresco y productos de vitrina listos para recoger.',
                        'El pedido por WhatsApp fue rapido y llego completo.',
                        'Buenos precios para abarrotes y snacks del dia.',
                        'El sandwich llego caliente y bien empacado.',
                        'Catalogo claro para comprar cafe, panaderia y basicos.',
                    ][$index] ?? 'Buen producto de minimarket.',
                    'estado' => 'Aprobado',
                ]
            );
        }
    }
}
