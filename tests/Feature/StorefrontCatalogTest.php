<?php

namespace Tests\Feature;

use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Modules\Auth\Models\Usuario;
use Modules\Inventory\Models\Categoria;
use Modules\Inventory\Models\Producto;
use Modules\Inventory\Models\ProductoPresentacion;
use Modules\Storefront\Models\Cliente;
use Modules\Storefront\Models\StorefrontSetting;
use Tests\TestCase;

class StorefrontCatalogTest extends TestCase
{
    use DatabaseTransactions;

    public function test_storefront_catalog_pages_render_with_seeded_inventory_data(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->get('/')
            ->assertOk()
            ->assertSee('Cafe americano KM2');

        $product = Producto::where('nombre_base', 'Cafe americano KM2')->firstOrFail();

        $this->get(route('storefront.producto', $product->id_producto))
            ->assertOk()
            ->assertSee('Cafe americano KM2')
            ->assertSee('Presentacion');

        $cliente = Cliente::where('email', 'maria@example.test')->firstOrFail();

        $this->withSession(['cliente_id' => $cliente->id_cliente])
            ->get(route('storefront.checkout'))
            ->assertOk()
            ->assertSee('IGV incluido');

        $this->getJson('/api/v1/storefront/products')
            ->assertOk()
            ->assertJsonFragment(['nombre' => 'Cafe americano KM2']);
    }

    public function test_checkout_recalculates_cart_from_presentations_and_creates_whatsapp_order(): void
    {
        $this->seed(DatabaseSeeder::class);

        $presentation = ProductoPresentacion::where('codigo_barras', 'KM2-CAF-AMER-8')->firstOrFail();

        $cliente = Cliente::where('email', 'maria@example.test')->firstOrFail();

        $response = $this->withSession(['cliente_id' => $cliente->id_cliente])
            ->post(route('storefront.store_pedido'), [
            'nombre' => 'Cliente Demo',
            'numero_whatsapp' => '51988887777',
            'direccion' => 'Av. Demo 123',
            'referencia' => 'Puerta principal',
            'id_zona' => 1,
            'cart' => json_encode([
                [
                    'id' => $presentation->id_presentacion,
                    'presentation_id' => $presentation->id_presentacion,
                    'product_id' => $presentation->id_producto,
                    'quantity' => 2,
                ],
            ]),
        ]);

        $response->assertStatus(302);
        $this->assertStringContainsString('https://wa.me/', $response->headers->get('Location'));

        $this->assertDatabaseHas('detalle_pedidos_tienda', [
            'id_presentacion' => $presentation->id_presentacion,
            'cantidad_solicitada' => 2,
            'cantidad_confirmada' => 2,
        ]);

        $this->assertDatabaseHas('presentaciones_producto', [
            'id_presentacion' => $presentation->id_presentacion,
            'stock' => 38,
        ]);
    }

    public function test_catalog_mode_allows_orders_with_zero_stock_without_stock_movements(): void
    {
        $this->seed(DatabaseSeeder::class);

        StorefrontSetting::current()->update(['control_stock_habilitado' => false]);

        $presentation = ProductoPresentacion::where('codigo_barras', 'KM2-CAF-AMER-8')->firstOrFail();
        $presentation->update(['stock' => 0]);

        $cliente = Cliente::where('email', 'maria@example.test')->firstOrFail();

        $response = $this->withSession(['cliente_id' => $cliente->id_cliente])
            ->post(route('storefront.store_pedido'), [
                'nombre' => 'Cliente Catalogo',
                'numero_whatsapp' => '51988887777',
                'direccion' => 'Av. Demo 456',
                'referencia' => 'Pedido sin control de stock',
                'id_zona' => 1,
                'cart' => json_encode([
                    [
                        'id' => $presentation->id_presentacion,
                        'presentation_id' => $presentation->id_presentacion,
                        'product_id' => $presentation->id_producto,
                        'quantity' => 3,
                    ],
                ]),
            ]);

        $response->assertStatus(302);
        $this->assertStringContainsString('https://wa.me/', $response->headers->get('Location'));

        $this->assertDatabaseHas('detalle_pedidos_tienda', [
            'id_presentacion' => $presentation->id_presentacion,
            'cantidad_solicitada' => 3,
            'cantidad_confirmada' => 3,
        ]);

        $this->assertDatabaseHas('presentaciones_producto', [
            'id_presentacion' => $presentation->id_presentacion,
            'stock' => 0,
        ]);

        $this->assertDatabaseMissing('movimientos_stock', [
            'id_presentacion' => $presentation->id_presentacion,
            'cantidad' => -3,
        ]);
    }

    public function test_unified_login_accepts_customer_accounts_and_preserves_storefront_session(): void
    {
        $this->seed(DatabaseSeeder::class);

        $response = $this->post(route('auth.login.submit'), [
            'email' => 'maria@example.test',
            'password' => 'cliente123',
        ]);

        $cliente = Cliente::where('email', 'maria@example.test')->firstOrFail();

        $response->assertRedirect(route('storefront.index'));
        $this->assertGuest();
        $this->assertSame($cliente->id_cliente, session('cliente_id'));
    }

    public function test_unified_login_accepts_internal_users_and_routes_to_dashboard(): void
    {
        $this->seed(DatabaseSeeder::class);

        $response = $this->post(route('auth.login.submit'), [
            'email' => 'admin@ponteready.com',
            'password' => 'admin123',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs(Usuario::findOrFail(1));
        $this->assertFalse(session()->has('cliente_id'));
    }

    public function test_seeded_internal_roles_match_store_pilot_scope(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertSame(
            ['Superadministrador', 'Administrador', 'Operador'],
            DB::table('roles_sistema')->orderBy('nivel_acceso')->pluck('nombre_rol')->all()
        );

        $this->assertDatabaseMissing('roles_sistema', ['nombre_rol' => 'Atencion WhatsApp']);
        $this->assertDatabaseMissing('roles_sistema', ['nombre_rol' => 'Operador WhatsApp']);
    }

    public function test_admin_can_manage_categories_and_subcategories(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = Usuario::findOrFail(1);
        $parent = Categoria::where('nombre', 'Cafeteria')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('admin.categorias.index'))
            ->assertOk()
            ->assertSee('Categorias y Subcategorias')
            ->assertSee('Cafeteria');

        $this->actingAs($admin)
            ->post(route('admin.categorias.store'), [
                'nombre' => 'Jugos naturales',
                'id_categoria_padre' => $parent->id_categoria,
                'estado' => 'Activo',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('categorias_producto', [
            'nombre' => 'Jugos naturales',
            'id_categoria_padre' => $parent->id_categoria,
            'estado' => 'Activo',
        ]);
    }

    public function test_storefront_api_client_auth_can_manage_database_cart(): void
    {
        $this->seed(DatabaseSeeder::class);

        $cliente = Cliente::create([
            'nombre_o_razon_social' => 'Cliente API',
            'email' => 'cliente.api@example.test',
            'celular' => '51977776666',
            'password' => Hash::make('secret123'),
        ]);
        $presentation = ProductoPresentacion::where('codigo_barras', 'KM2-CAF-AMER-8')->firstOrFail();

        $this->postJson('/api/v1/storefront/login', [
            'email' => 'cliente.api@example.test',
            'password' => 'secret123',
        ])
            ->assertOk()
            ->assertJsonPath('cliente.id', $cliente->id_cliente)
            ->assertJsonPath('cliente.numero_whatsapp', '51977776666')
            ->assertJsonStructure(['token']);

        Sanctum::actingAs($cliente);

        $this->postJson('/api/v1/storefront/cart', [
            'id_presentacion' => $presentation->id_presentacion,
            'cantidad' => 1,
        ])
            ->assertCreated()
            ->assertJsonFragment([
                'id_cliente' => $cliente->id_cliente,
                'id_presentacion' => $presentation->id_presentacion,
                'cantidad' => 1,
            ]);

        $this->assertDatabaseHas('carrito_items', [
            'id_cliente' => $cliente->id_cliente,
            'id_presentacion' => $presentation->id_presentacion,
            'cantidad' => 1,
        ]);

        $this->getJson('/api/v1/storefront/cart')
            ->assertOk()
            ->assertJsonFragment(['id_presentacion' => $presentation->id_presentacion]);
    }

    public function test_storefront_api_register_uses_numero_whatsapp_contract(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->postJson('/api/v1/storefront/register', [
            'nombre' => 'Cliente WhatsApp API',
            'email' => 'cliente.whatsapp.api@example.test',
            'numero_whatsapp' => '51966665555',
            'password' => 'secret123',
        ])
            ->assertCreated()
            ->assertJsonPath('cliente.numero_whatsapp', '51966665555');

        $this->assertDatabaseHas('clientes_web', [
            'email' => 'cliente.whatsapp.api@example.test',
            'celular' => '51966665555',
        ]);
    }

    public function test_admin_can_manage_banners_images_and_product_promotions(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = Usuario::findOrFail(1);

        $this->actingAs($admin)
            ->get(route('admin.banners.index'))
            ->assertOk()
            ->assertSee('Banners y Promociones');

        $this->actingAs($admin)
            ->post(route('admin.banners.store'), [
                'titulo' => 'Promo desayuno KM2',
                'imagen_url' => 'https://example.com/banner-desayuno.jpg',
                'link_destino' => '/?categoria_id=5',
                'posicion' => 'Carrusel',
                'estado' => 'Activo',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('banners_tienda', [
            'titulo' => 'Promo desayuno KM2',
            'imagen_url' => 'https://example.com/banner-desayuno.jpg',
            'estado' => 'Activo',
        ]);

        $bannerId = \Modules\Storefront\Models\BannerWeb::where('titulo', 'Promo desayuno KM2')->value('id_banner');

        $this->actingAs($admin)
            ->post(route('admin.banners.update', $bannerId), [
                'titulo' => 'Promo desayuno actualizado',
                'imagen_url' => 'https://example.com/banner-actualizado.jpg',
                'link_destino' => '/?categoria_id=6',
                'posicion' => 'Carrusel',
                'estado' => 'Activo',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('banners_tienda', [
            'id_banner' => $bannerId,
            'titulo' => 'Promo desayuno actualizado',
            'imagen_url' => 'https://example.com/banner-actualizado.jpg',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.banners.store'), [
                'titulo' => 'Lateral bebidas frias',
                'imagen_url' => 'https://example.com/banner-lateral.jpg',
                'link_destino' => '/?categoria_id=3',
                'posicion' => 'Lateral',
                'estado' => 'Activo',
            ])
            ->assertRedirect();

        $this->actingAs($admin)
            ->post(route('admin.banners.store'), [
                'titulo' => 'Pop up combo oficina',
                'imagen_url' => 'https://example.com/banner-popup.jpg',
                'link_destino' => '/?categoria_id=6',
                'posicion' => 'Pop_up',
                'estado' => 'Activo',
            ])
            ->assertRedirect();

        $bannerAdminHtml = $this->actingAs($admin)->get(route('admin.banners.index'))->getContent();
        $this->assertStringNotContainsString("@click='editMode = true; currentItem = JSON.parse('", $bannerAdminHtml);

        $product = Producto::where('nombre_base', 'Cafe americano KM2')->firstOrFail();
        $presentation = ProductoPresentacion::where('id_producto', $product->id_producto)->firstOrFail();

        $this->actingAs($admin)
            ->post(route('admin.productos.update', $product->id_producto), [
                'nombre' => $product->nombre_base,
                'descripcion' => $product->descripcion,
                'id_categoria' => $product->id_categoria,
                'precio_venta' => 5.50,
                'precio_referencial' => 6.50,
                'stock' => 30,
                'estado' => 'Activo',
                'foto_url' => 'https://example.com/cafe-americano.jpg',
                'galeria_urls' => "https://example.com/cafe-americano.jpg\nhttps://example.com/cafe-barra.jpg",
                'nombre_variante' => $presentation->nombre_variante,
                'codigo_barras' => $presentation->codigo_barras,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('presentaciones_producto', [
            'id_presentacion' => $presentation->id_presentacion,
            'precio' => 5.50,
            'precio_referencial' => 6.50,
            'stock' => 30,
        ]);

        $this->assertDatabaseHas('imagenes_producto', [
            'id_producto' => $product->id_producto,
            'imagen_url' => 'https://example.com/cafe-barra.jpg',
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('Ofertas activas')
            ->assertSee('Cafe americano KM2')
            ->assertSee('Lateral bebidas frias')
            ->assertSee('Pop up combo oficina')
            ->assertSee('https://example.com/banner-lateral.jpg', false)
            ->assertSee('https://example.com/banner-popup.jpg', false)
            ->assertSee('sessionStorage', false);
    }
}
