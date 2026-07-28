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

        // 1. Dinamis mencari dan menghapus semua foreign key yang mengarah ke outlets.id
        $dbName = DB::connection()->getDatabaseName();
        $fks = DB::select("
            SELECT TABLE_NAME, CONSTRAINT_NAME 
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE REFERENCED_TABLE_NAME = 'outlets' 
              AND REFERENCED_COLUMN_NAME = 'id' 
              AND TABLE_SCHEMA = ?
        ", [$dbName]);

        foreach ($fks as $fk) {
            DB::statement("ALTER TABLE `{$fk->TABLE_NAME}` DROP FOREIGN KEY `{$fk->CONSTRAINT_NAME}`");
        }

        // 2. Modifikasi tipe data Primary Key di tabel outlets
        DB::statement('ALTER TABLE outlets MODIFY id VARCHAR(10) NOT NULL;');

        // 3. Modifikasi kolom outlet_id di tabel-tabel anak
        $notNullTables = [
            'outlet_product', 'orders', 'tables', 'shifts', 'shift_schedules',
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

        // 4. Tambahkan kembali relasi Foreign Key
        $cascadeTables = [
            'outlet_product', 'orders', 'tables', 'shifts', 'shift_schedules',
            'history_transactions', 'stock_histories', 'taxes'
        ];
        foreach ($cascadeTables as $table) {
            if (Schema::hasColumn($table, 'outlet_id')) {
                // Pastikan tidak ada constraint dengan nama default ini sebelumnya
                // Abaikan error jika sudah ada
                try {
                    Schema::table($table, function (Blueprint $t) {
                        $t->foreign('outlet_id')->references('id')->on('outlets')->onDelete('cascade');
                    });
                } catch (\Exception $e) {
                    // Abaikan jika gagal menambah ulang foreign key
                }
            }
        }
        
        if (Schema::hasColumn('shift_karyawans', 'outlet_id')) {
            try {
                Schema::table('shift_karyawans', function (Blueprint $t) {
                    $t->foreign('outlet_id')->references('id')->on('outlets')->onDelete('set null');
                });
            } catch (\Exception $e) {}
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $dbName = DB::connection()->getDatabaseName();
        $fks = DB::select("
            SELECT TABLE_NAME, CONSTRAINT_NAME 
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE REFERENCED_TABLE_NAME = 'outlets' 
              AND REFERENCED_COLUMN_NAME = 'id' 
              AND TABLE_SCHEMA = ?
        ", [$dbName]);

        foreach ($fks as $fk) {
            DB::statement("ALTER TABLE `{$fk->TABLE_NAME}` DROP FOREIGN KEY `{$fk->CONSTRAINT_NAME}`");
        }

        DB::statement('ALTER TABLE outlets MODIFY id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT;');

        $notNullTables = [
            'outlet_product', 'orders', 'tables', 'shifts', 'shift_schedules',
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

        $cascadeTables = [
            'outlet_product', 'orders', 'tables', 'shifts', 'shift_schedules',
            'history_transactions', 'stock_histories', 'taxes'
        ];
        foreach ($cascadeTables as $table) {
            if (Schema::hasColumn($table, 'outlet_id')) {
                try {
                    Schema::table($table, function (Blueprint $t) {
                        $t->foreign('outlet_id')->references('id')->on('outlets')->onDelete('cascade');
                    });
                } catch (\Exception $e) {}
            }
        }
        
        if (Schema::hasColumn('shift_karyawans', 'outlet_id')) {
            try {
                Schema::table('shift_karyawans', function (Blueprint $t) {
                    $t->foreign('outlet_id')->references('id')->on('outlets')->onDelete('set null');
                });
            } catch (\Exception $e) {}
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
};
