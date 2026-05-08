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

        if (Schema::hasTable('clientes')) {
            Schema::table('clientes', function (Blueprint $table) {
                foreach (['limite_credito', 'credito_usado', 'puntos_acumulados'] as $column) {
                    if (Schema::hasColumn('clientes', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }

        if (Schema::hasTable('empresa_configuracion')) {
            Schema::table('empresa_configuracion', function (Blueprint $table) {
                foreach ($this->hardwareColumns() as $column) {
                    if (Schema::hasColumn('empresa_configuracion', $column)) {
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

    private function hardwareColumns(): array
    {
        return [
            'printer_enabled',
            'printer_name',
            'printer_connection_type',
            'printer_target_host',
            'printer_device_reference',
            'printer_paper_width',
            'printer_auto_print',
            'printer_copies',
            'scanner_enabled',
            'scanner_device_name',
            'scanner_input_mode',
            'scanner_accept_qr',
            'scanner_accept_barcode',
            'scanner_suffix_enter',
        ];
    }
};
