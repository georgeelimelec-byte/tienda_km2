<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('configuracion_tienda', function (Blueprint $table) {
            $table->id();
            $table->string('store_name', 80)->default('Market KM2');
            $table->string('store_tagline', 120)->default('Minimarket & Cafe');
            $table->string('logo_url', 255)->nullable();
            $table->string('primary_color', 7)->default('#f97316');
            $table->string('primary_light_color', 7)->default('#fb923c');
            $table->string('primary_dark_color', 7)->default('#ea580c');
            $table->string('accent_color', 7)->default('#1f2937');
            $table->enum('header_style', ['solid', 'dark'])->default('solid');
            $table->enum('card_style', ['rounded', 'compact', 'flat'])->default('rounded');
            $table->boolean('show_login_link')->default(true);
            $table->string('footer_text', 160)->nullable();
            $table->string('whatsapp_number', 24)->nullable();
            $table->string('contact_phone', 24)->nullable();
            $table->string('contact_email', 120)->nullable();
            $table->string('currency', 10)->default('PEN');
            $table->decimal('included_tax_percent', 5, 2)->default(18.00);
            $table->string('business_hours', 160)->nullable();
            $table->text('operational_message')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('configuracion_tienda');
    }
};
