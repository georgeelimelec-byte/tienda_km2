<?php

namespace Modules\Inventory\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Inventory\Models\Producto;
use Modules\Inventory\Models\ProductoImagen;
use Modules\Inventory\Models\ProductoPresentacion;

class InventoryDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedUnits();
        $this->seedCategories();
        $this->disableOldDemoProducts();
        $this->seedProducts();
    }

    private function seedUnits(): void
    {
        foreach ([
            ['id_unidad' => 1, 'nombre' => 'Unidad', 'abreviatura' => 'UND', 'estado' => 'Activo'],
            ['id_unidad' => 2, 'nombre' => 'Litro', 'abreviatura' => 'LT', 'estado' => 'Activo'],
            ['id_unidad' => 3, 'nombre' => 'Kilogramo', 'abreviatura' => 'KG', 'estado' => 'Activo'],
            ['id_unidad' => 4, 'nombre' => 'Pack', 'abreviatura' => 'PK', 'estado' => 'Activo'],
            ['id_unidad' => 5, 'nombre' => 'Gramos', 'abreviatura' => 'GR', 'estado' => 'Activo'],
        ] as $unit) {
            DB::table('unidades_medida')->updateOrInsert(['id_unidad' => $unit['id_unidad']], $unit);
        }
    }

    private function seedCategories(): void
    {
        foreach ([
            ['id_categoria' => 1, 'id_categoria_padre' => null, 'nombre' => 'Minimarket', 'estado' => 'Activo'],
            ['id_categoria' => 2, 'id_categoria_padre' => 1, 'nombre' => 'Abarrotes y despensa', 'estado' => 'Activo'],
            ['id_categoria' => 3, 'id_categoria_padre' => 1, 'nombre' => 'Bebidas frias y snacks', 'estado' => 'Activo'],
            ['id_categoria' => 4, 'id_categoria_padre' => null, 'nombre' => 'Cafeteria', 'estado' => 'Activo'],
            ['id_categoria' => 5, 'id_categoria_padre' => 4, 'nombre' => 'Cafe y bebidas calientes', 'estado' => 'Activo'],
            ['id_categoria' => 6, 'id_categoria_padre' => 4, 'nombre' => 'Sandwiches y salados', 'estado' => 'Activo'],
            ['id_categoria' => 7, 'id_categoria_padre' => 4, 'nombre' => 'Panaderia y postres', 'estado' => 'Activo'],
            ['id_categoria' => 8, 'id_categoria_padre' => 1, 'nombre' => 'Lacteos y refrigerados', 'estado' => 'Activo'],
            ['id_categoria' => 9, 'id_categoria_padre' => 1, 'nombre' => 'Cuidado y limpieza', 'estado' => 'Activo'],
        ] as $category) {
            DB::table('categorias_producto')->updateOrInsert(['id_categoria' => $category['id_categoria']], $category);
        }
    }

    private function disableOldDemoProducts(): void
    {
        $oldNames = [
            'Smartphone Galaxy A55',
            'Audifonos Bluetooth Pulse',
            'Cargador USB-C 25W',
            'Freidora de aire 5L',
            'Set de envases hermeticos',
            'Detergente liquido Fresh',
            'Shampoo reparacion intensa',
        ];

        Producto::whereIn('nombre_base', $oldNames)->update(['estado' => 'Inactivo']);
        ProductoPresentacion::whereIn('codigo_barras', [
            'KM2-A55-128-BLK',
            'KM2-A55-256-BLU',
            'KM2-PULSE-WHT',
            'KM2-PULSE-BLK',
            'KM2-CHG-25W',
            'KM2-AIRFRY-5L',
            'KM2-ENV-6',
            'KM2-ENV-10',
            'KM2-DETER-3L',
            'KM2-SHAM-750',
        ])->update(['estado' => 'Inactivo']);
    }

    private function seedProducts(): void
    {
        $products = [
            [
                'category' => 5,
                'name' => 'Cafe americano KM2',
                'description' => 'Cafe pasado al momento, taza caliente y aroma de barra para llevar o disfrutar en tienda.',
                'image' => 'https://images.unsplash.com/photo-1509042239860-f550ce710b93?auto=format&fit=crop&q=80&w=900',
                'presentations' => [
                    ['variant' => 'Vaso 8 oz', 'barcode' => 'KM2-CAF-AMER-8', 'price' => 6.50, 'offer' => null, 'stock' => 40],
                    ['variant' => 'Vaso 12 oz', 'barcode' => 'KM2-CAF-AMER-12', 'price' => 8.50, 'offer' => null, 'stock' => 35],
                ],
            ],
            [
                'category' => 5,
                'name' => 'Capuccino artesanal',
                'description' => 'Espresso con leche vaporizada y espuma cremosa, preparado en barra de cafeteria.',
                'image' => 'https://images.unsplash.com/photo-1534778101976-62847782c213?auto=format&fit=crop&q=80&w=900',
                'presentations' => [
                    ['variant' => 'Vaso 12 oz', 'barcode' => 'KM2-CAP-12', 'price' => 10.90, 'offer' => 9.90, 'stock' => 25],
                ],
            ],
            [
                'category' => 6,
                'name' => 'Sandwich mixto caliente',
                'description' => 'Pan suave con jamon y queso fundido, servido caliente para desayuno o lonche.',
                'image' => 'https://images.unsplash.com/photo-1528735602780-2552fd46c7af?auto=format&fit=crop&q=80&w=900',
                'presentations' => [
                    ['variant' => 'Unidad', 'barcode' => 'KM2-SAND-MIX', 'price' => 8.90, 'offer' => null, 'stock' => 18],
                ],
            ],
            [
                'category' => 7,
                'name' => 'Croissant de mantequilla',
                'description' => 'Croissant hojaldrado de vitrina, ideal para acompanar cafe o bebida caliente.',
                'image' => 'https://images.unsplash.com/photo-1555507036-ab1f4038808a?auto=format&fit=crop&q=80&w=900',
                'presentations' => [
                    ['variant' => 'Unidad', 'barcode' => 'KM2-CROIS-UND', 'price' => 5.90, 'offer' => null, 'stock' => 24],
                    ['variant' => 'Pack x 4', 'barcode' => 'KM2-CROIS-4', 'price' => 21.90, 'offer' => 19.90, 'stock' => 8],
                ],
            ],
            [
                'category' => 7,
                'name' => 'Brownie de chocolate',
                'description' => 'Brownie humedo con cacao intenso, porcion individual lista para llevar.',
                'image' => 'https://images.unsplash.com/photo-1606313564200-e75d5e30476c?auto=format&fit=crop&q=80&w=900',
                'presentations' => [
                    ['variant' => 'Porcion', 'barcode' => 'KM2-BROWNIE-POR', 'price' => 6.90, 'offer' => null, 'stock' => 16],
                ],
            ],
            [
                'category' => 3,
                'name' => 'Agua mineral sin gas',
                'description' => 'Agua mineral fresca de vitrina refrigerada para acompanar pedidos o snacks.',
                'image' => 'https://images.unsplash.com/photo-1564419320461-6870880221ad?auto=format&fit=crop&q=80&w=900',
                'presentations' => [
                    ['variant' => 'Botella 625 ml', 'barcode' => 'KM2-AGUA-625', 'price' => 2.50, 'offer' => null, 'stock' => 60],
                    ['variant' => 'Pack x 6', 'barcode' => 'KM2-AGUA-6', 'price' => 13.90, 'offer' => 12.90, 'stock' => 20],
                ],
            ],
            [
                'category' => 3,
                'name' => 'Papas nativas crocantes',
                'description' => 'Snack salado de papas nativas, bolsa lista para lonchera, oficina o camino.',
                'image' => 'https://images.unsplash.com/photo-1566478989037-eec170784d0b?auto=format&fit=crop&q=80&w=900',
                'presentations' => [
                    ['variant' => 'Bolsa 120 g', 'barcode' => 'KM2-PAPAS-120', 'price' => 6.50, 'offer' => null, 'stock' => 30],
                ],
            ],
            [
                'category' => 2,
                'name' => 'Arroz extra seleccionado',
                'description' => 'Arroz de grano largo para despensa diaria del hogar, disponible por bolsa.',
                'image' => 'https://images.unsplash.com/photo-1586201375761-83865001e31c?auto=format&fit=crop&q=80&w=900',
                'presentations' => [
                    ['variant' => 'Bolsa 1 kg', 'barcode' => 'KM2-ARROZ-1K', 'price' => 5.80, 'offer' => null, 'stock' => 40],
                    ['variant' => 'Bolsa 5 kg', 'barcode' => 'KM2-ARROZ-5K', 'price' => 27.90, 'offer' => 25.90, 'stock' => 14],
                ],
            ],
            [
                'category' => 2,
                'name' => 'Aceite vegetal premium',
                'description' => 'Aceite vegetal para cocina diaria, ideal para pedidos rapidos de minimarket.',
                'image' => 'https://images.unsplash.com/photo-1474979266404-7eaacbcd87c5?auto=format&fit=crop&q=80&w=900',
                'presentations' => [
                    ['variant' => 'Botella 1 L', 'barcode' => 'KM2-ACEITE-1L', 'price' => 10.90, 'offer' => null, 'stock' => 26],
                ],
            ],
            [
                'category' => 8,
                'name' => 'Yogurt bebible fresa',
                'description' => 'Yogurt refrigerado sabor fresa, listo para desayuno, snack o lonchera.',
                'image' => 'https://images.unsplash.com/photo-1571212515416-fef01fc43637?auto=format&fit=crop&q=80&w=900',
                'presentations' => [
                    ['variant' => 'Botella 1 L', 'barcode' => 'KM2-YOG-FRESA-1L', 'price' => 8.90, 'offer' => null, 'stock' => 18],
                    ['variant' => 'Botella 200 ml', 'barcode' => 'KM2-YOG-FRESA-200', 'price' => 2.80, 'offer' => null, 'stock' => 36],
                ],
            ],
            [
                'category' => 8,
                'name' => 'Queso fresco artesanal',
                'description' => 'Queso fresco de vitrina refrigerada para desayuno, sandwich o cocina casera.',
                'image' => 'https://images.unsplash.com/photo-1486297678162-eb2a19b0a32d?auto=format&fit=crop&q=80&w=900',
                'presentations' => [
                    ['variant' => 'Molde 500 g', 'barcode' => 'KM2-QUESO-500', 'price' => 13.90, 'offer' => null, 'stock' => 12],
                ],
            ],
            [
                'category' => 9,
                'name' => 'Detergente liquido multiuso',
                'description' => 'Producto esencial de limpieza para pedidos de reposicion del hogar.',
                'image' => 'https://images.unsplash.com/photo-1626806819282-2c1dc01a5e0c?auto=format&fit=crop&q=80&w=900',
                'presentations' => [
                    ['variant' => 'Botella 3 L', 'barcode' => 'KM2-LIMP-DETER-3L', 'price' => 28.90, 'offer' => null, 'stock' => 18],
                ],
            ],
        ];

        foreach ($products as $productData) {
            $product = Producto::updateOrCreate(
                ['nombre_base' => $productData['name']],
                [
                    'id_categoria' => $productData['category'],
                    'descripcion' => $productData['description'],
                    'imagen_url' => $productData['image'],
                    'estado' => 'Activo',
                ]
            );

            ProductoImagen::updateOrCreate(
                ['id_producto' => $product->id_producto, 'orden' => 0],
                ['imagen_url' => $productData['image']]
            );

            foreach ($productData['presentations'] as $presentationData) {
                $presentation = ProductoPresentacion::updateOrCreate(
                    ['codigo_barras' => $presentationData['barcode']],
                    [
                        'id_producto' => $product->id_producto,
                        'id_unidad' => 1,
                        'nombre_variante' => $presentationData['variant'],
                        'costo_reposicion' => 0,
                        'precio' => $presentationData['offer'] ?? $presentationData['price'],
                        'precio_referencial' => $presentationData['offer'] ? $presentationData['price'] : null,
                        'stock_web' => $presentationData['stock'],
                        'stock_web_minimo' => 3,
                        'estado' => 'Activo',
                    ]
                );

            }
        }
    }
}
