<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('storefront_settings')) {
            Schema::table('storefront_settings', function (Blueprint $table) {
                if (! Schema::hasColumn('storefront_settings', 'whatsapp_number')) {
                    $table->string('whatsapp_number', 24)->nullable()->after('footer_text');
                }

                if (! Schema::hasColumn('storefront_settings', 'contact_phone')) {
                    $table->string('contact_phone', 24)->nullable()->after('whatsapp_number');
                }

                if (! Schema::hasColumn('storefront_settings', 'contact_email')) {
                    $table->string('contact_email', 120)->nullable()->after('contact_phone');
                }

                if (! Schema::hasColumn('storefront_settings', 'currency')) {
                    $table->string('currency', 10)->default('PEN')->after('contact_email');
                }

                if (! Schema::hasColumn('storefront_settings', 'included_tax_percent')) {
                    $table->decimal('included_tax_percent', 5, 2)->default(18.00)->after('currency');
                }

                if (! Schema::hasColumn('storefront_settings', 'business_hours')) {
                    $table->string('business_hours', 160)->nullable()->after('included_tax_percent');
                }

                if (! Schema::hasColumn('storefront_settings', 'operational_message')) {
                    $table->text('operational_message')->nullable()->after('business_hours');
                }
            });
        }

        if (Schema::hasTable('empresa_configuracion')) {
            $company = DB::table('empresa_configuracion')->first();

            if ($company && Schema::hasTable('storefront_settings')) {
                DB::table('storefront_settings')->updateOrInsert(
                    ['id' => 1],
                    [
                        'store_name' => $company->nombre_comercial ?: 'Market KM2',
                        'logo_url' => $company->logo_url ?: null,
                        'contact_phone' => $company->telefono_contacto,
                        'contact_email' => $company->correo_contacto,
                        'currency' => $company->moneda ?? 'PEN',
                        'included_tax_percent' => $company->porcentaje_igv ?? 18.00,
                        'business_hours' => $company->horario_atencion ?? null,
                        'operational_message' => $company->mensaje_operativo ?? null,
                        'updated_at' => now(),
                    ]
                );
            }

            Schema::dropIfExists('empresa_configuracion');
        }

        DB::table('migrations')
            ->where('migration', '2026_04_26_000001_add_operational_fields_to_empresa_configuracion')
            ->delete();
    }

    public function down(): void
    {
        //
    }
};
