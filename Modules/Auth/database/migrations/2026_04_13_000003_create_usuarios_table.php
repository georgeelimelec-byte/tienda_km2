<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migracion para la tabla 'usuarios_internos'.
 * Usuarios del sistema para administracion de tienda, pedidos y catalogo.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usuarios_internos', function (Blueprint $table) {
            $table->increments('id_usuario');
            $table->unsignedInteger('id_rol');
            $table->string('nombres', 100);
            $table->string('email', 100)->unique()->nullable();
            $table->string('password_hash', 255);
            $table->string('foto_url', 255)->default('default-user.png');
            $table->enum('estado', ['Activo', 'Inactivo'])->default('Activo');
            $table->timestamp('fecha_registro')->useCurrent();

            $table->foreign('id_rol')
                ->references('id_rol')->on('roles_sistema')
                ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuarios_internos');
    }
};
