<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Pastikan tabel ada
        if (Schema::hasColumn('outlet_product', 'outlet_id')) {
            // Modifikasi kolom menjadi VARCHAR(10)
            DB::statement("ALTER TABLE outlet_product MODIFY outlet_id VARCHAR(10) NOT NULL;");

            // Pasang kembali foreign key
            try {
                Schema::table('outlet_product', function (Blueprint $t) {
                    $t->foreign('outlet_id')->references('id')->on('outlets')->onDelete('cascade');
                });
            } catch (\Exception $e) {
                // Abaikan jika sudah ada
            }
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    public function down(): void
    {
        // Revert tidak diperlukan karena ini script perbaikan patch
    }
};
