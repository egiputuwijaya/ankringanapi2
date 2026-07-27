<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $cascadeTables = [
            'products', 'orders', 'tables', 'shifts', 'shift_schedules',
            'history_transactions', 'stock_histories', 'taxes'
        ];
        
        foreach ($cascadeTables as $table) {
            if (Schema::hasColumn($table, 'outlet_id')) {
                Schema::table($table, function (Blueprint $t) {
                    $t->dropForeign(['outlet_id']);
                });
            }
        }

        if (Schema::hasColumn('shift_karyawans', 'outlet_id')) {
            Schema::table('shift_karyawans', function (Blueprint $t) {
                $t->dropForeign(['outlet_id']);
            });
        }

        // Modify columns
        DB::statement('ALTER TABLE outlets MODIFY id VARCHAR(10) NOT NULL;');

        $notNullTables = [
            'products', 'orders', 'tables', 'shifts', 'shift_schedules',
            'history_transactions', 'stock_histories', 'invoice_counters'
        ];
        foreach ($notNullTables as $table) {
            if (Schema::hasColumn($table, 'outlet_id')) {
                DB::statement("ALTER TABLE {$table} MODIFY outlet_id VARCHAR(10) NOT NULL;");
            }
        }

        $nullTables = ['users', 'shift_karyawans', 'taxes'];
        foreach ($nullTables as $table) {
            if (Schema::hasColumn($table, 'outlet_id')) {
                DB::statement("ALTER TABLE {$table} MODIFY outlet_id VARCHAR(10) NULL;");
            }
        }

        // Re-add foreign keys
        foreach ($cascadeTables as $table) {
            if (Schema::hasColumn($table, 'outlet_id')) {
                Schema::table($table, function (Blueprint $t) {
                    $t->foreign('outlet_id')->references('id')->on('outlets')->onDelete('cascade');
                });
            }
        }
        
        if (Schema::hasColumn('shift_karyawans', 'outlet_id')) {
            Schema::table('shift_karyawans', function (Blueprint $t) {
                $t->foreign('outlet_id')->references('id')->on('outlets')->onDelete('set null');
            });
        }
    }

    public function down(): void
    {
        $cascadeTables = [
            'products', 'orders', 'tables', 'shifts', 'shift_schedules',
            'history_transactions', 'stock_histories', 'taxes'
        ];
        
        foreach ($cascadeTables as $table) {
            if (Schema::hasColumn($table, 'outlet_id')) {
                Schema::table($table, function (Blueprint $t) {
                    $t->dropForeign(['outlet_id']);
                });
            }
        }

        if (Schema::hasColumn('shift_karyawans', 'outlet_id')) {
            Schema::table('shift_karyawans', function (Blueprint $t) {
                $t->dropForeign(['outlet_id']);
            });
        }

        DB::statement('ALTER TABLE outlets MODIFY id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT;');

        $notNullTables = [
            'products', 'orders', 'tables', 'shifts', 'shift_schedules',
            'history_transactions', 'stock_histories', 'invoice_counters'
        ];
        foreach ($notNullTables as $table) {
            if (Schema::hasColumn($table, 'outlet_id')) {
                DB::statement("ALTER TABLE {$table} MODIFY outlet_id BIGINT UNSIGNED NOT NULL;");
            }
        }

        $nullTables = ['users', 'shift_karyawans', 'taxes'];
        foreach ($nullTables as $table) {
            if (Schema::hasColumn($table, 'outlet_id')) {
                DB::statement("ALTER TABLE {$table} MODIFY outlet_id BIGINT UNSIGNED NULL;");
            }
        }

        foreach ($cascadeTables as $table) {
            if (Schema::hasColumn($table, 'outlet_id')) {
                Schema::table($table, function (Blueprint $t) {
                    $t->foreign('outlet_id')->references('id')->on('outlets')->onDelete('cascade');
                });
            }
        }
        
        if (Schema::hasColumn('shift_karyawans', 'outlet_id')) {
            Schema::table('shift_karyawans', function (Blueprint $t) {
                $t->foreign('outlet_id')->references('id')->on('outlets')->onDelete('set null');
            });
        }
    }
};
