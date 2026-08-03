<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('office_supply_logs', 'item_name')) {
            Schema::table('office_supply_logs', function (Blueprint $table) {
                $table->string('item_name')->nullable()->after('office_supply_id');
            });
        }

        try {
            DB::statement('ALTER TABLE office_supply_logs MODIFY office_supply_id BIGINT UNSIGNED NULL');
        } catch (\Exception $e) {}

        try {
            DB::statement('ALTER TABLE office_supply_logs DROP FOREIGN KEY office_supply_logs_office_supply_id_foreign');
        } catch (\Exception $e) {}

        try {
            DB::statement('ALTER TABLE office_supply_logs ADD CONSTRAINT office_supply_logs_office_supply_id_foreign FOREIGN KEY (office_supply_id) REFERENCES office_supplies(id) ON DELETE SET NULL');
        } catch (\Exception $e) {}
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('office_supply_logs', 'item_name')) {
            Schema::table('office_supply_logs', function (Blueprint $table) {
                $table->dropColumn('item_name');
            });
        }
    }
};
