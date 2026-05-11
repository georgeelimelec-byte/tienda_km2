<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        foreach ([
            'notificaciones_admin',
            'auditoria_log',
            'direcciones_cliente',
            'cupones_descuento',
            'repartidores',
            'recetas_combos',
        ] as $table) {
            Schema::dropIfExists($table);
        }

        Schema::enableForeignKeyConstraints();

        if (Schema::hasTable('clientes_web')) {
            Schema::table('clientes_web', function (Blueprint $table) {
                foreach (['limite_credito', 'credito_usado', 'puntos_acumulados'] as $column) {
                    if (Schema::hasColumn('clientes_web', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }

    }

    public function down(): void
    {
        // The dropped tables/columns belonged to removed or unimplemented modules.
    }

};
