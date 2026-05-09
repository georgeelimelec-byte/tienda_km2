<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('resenas');

        DB::table('migrations')
            ->where('migration', '2026_04_13_000026_create_resenas_table')
            ->delete();
    }

    public function down(): void
    {
        //
    }
};
