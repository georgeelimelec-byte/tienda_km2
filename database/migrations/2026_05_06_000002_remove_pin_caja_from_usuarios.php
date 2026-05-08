<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('usuarios') || ! Schema::hasColumn('usuarios', 'pin_caja')) {
            return;
        }

        Schema::table('usuarios', function (Blueprint $table) {
            $table->dropColumn('pin_caja');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('usuarios') || Schema::hasColumn('usuarios', 'pin_caja')) {
            return;
        }

        Schema::table('usuarios', function (Blueprint $table) {
            $table->string('pin_caja', 20)->nullable()->after('password_hash');
        });
    }
};
