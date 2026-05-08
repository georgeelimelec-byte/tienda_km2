<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('empresa_configuracion', function (Blueprint $table) {
            $table->increments('id_empresa');
            $table->string('ruc', 11);
            $table->string('razon_social', 150);
            $table->string('nombre_comercial', 150);
            $table->string('logo_url', 255)->default('logo_default.png');
            $table->text('direccion_fiscal');
            $table->string('telefono_contacto', 20)->nullable();
            $table->string('correo_contacto', 100)->nullable();
            $table->string('ubigeo', 6)->nullable();
            $table->decimal('porcentaje_igv', 5, 2)->default(18.00);
            $table->enum('estado', ['Activo', 'Inactivo'])->default('Activo');
        });

        Schema::create('personal_access_tokens', function (Blueprint $table) {
            $table->id();
            $table->morphs('tokenable');
            $table->string('name');
            $table->string('token', 64)->unique();
            $table->text('abilities')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('personal_access_tokens');
        Schema::dropIfExists('empresa_configuracion');
    }
};
