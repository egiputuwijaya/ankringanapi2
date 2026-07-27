<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('order_type', ['dine_in', 'take_away'])->default('dine_in')->after('table_id');
        });

        Schema::table('taxes', function (Blueprint $table) {
            $table->enum('apply_to', ['all', 'dine_in', 'take_away'])->default('all')->after('active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders_and_taxes_tables', function (Blueprint $table) {
            //
        });
    }
};
