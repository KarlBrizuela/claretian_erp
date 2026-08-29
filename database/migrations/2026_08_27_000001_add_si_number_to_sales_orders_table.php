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
        if (Schema::hasTable('sales_orders') && !Schema::hasColumn('sales_orders', 'si_number')) {
            Schema::table('sales_orders', function (Blueprint $table) {
                $table->string('si_number', 50)->nullable()->after('so_number');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('sales_orders') && Schema::hasColumn('sales_orders', 'si_number')) {
            Schema::table('sales_orders', function (Blueprint $table) {
                $table->dropColumn('si_number');
            });
        }
    }
};
