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
        if (!Schema::hasColumn('office_supply_logs', 'unit_price')) {
            Schema::table('office_supply_logs', function (Blueprint $table) {
                $table->decimal('unit_price', 10, 2)->default(0.00)->after('quantity');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('office_supply_logs', 'unit_price')) {
            Schema::table('office_supply_logs', function (Blueprint $table) {
                $table->dropColumn('unit_price');
            });
        }
    }
};
