<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        DB::statement('ALTER TABLE outlets MODIFY id VARCHAR(10) NOT NULL;');

        $notNullTables = [
            'categories',
            'products',
            'orders',
            'tables',
            'shifts',
            'shift_schedules',
            'history_transactions',
            'stock_histories',
            'invoice_counters'
        ];
        
        $nullTables = [
            'users',
            'shift_karyawans',
            'taxes'
        ];

        foreach ($notNullTables as $table) {
            DB::statement("ALTER TABLE {$table} MODIFY outlet_id VARCHAR(10) NOT NULL;");
        }

        foreach ($nullTables as $table) {
            DB::statement("ALTER TABLE {$table} MODIFY outlet_id VARCHAR(10) NULL;");
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        DB::statement('ALTER TABLE outlets MODIFY id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT;');

        $notNullTables = [
            'categories',
            'products',
            'orders',
            'tables',
            'shifts',
            'shift_schedules',
            'history_transactions',
            'stock_histories',
            'invoice_counters'
        ];
        
        $nullTables = [
            'users',
            'shift_karyawans',
            'taxes'
        ];

        foreach ($notNullTables as $table) {
            DB::statement("ALTER TABLE {$table} MODIFY outlet_id BIGINT UNSIGNED NOT NULL;");
        }

        foreach ($nullTables as $table) {
            DB::statement("ALTER TABLE {$table} MODIFY outlet_id BIGINT UNSIGNED NULL;");
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
};
