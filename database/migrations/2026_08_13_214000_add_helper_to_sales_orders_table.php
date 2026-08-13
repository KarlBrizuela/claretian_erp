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
        if (!Schema::hasColumn('sales_orders', 'helper')) {
            Schema::table('sales_orders', function (Blueprint $table) {
                $table->string('helper')->nullable()->after('plate_number');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('sales_orders', 'helper')) {
            Schema::table('sales_orders', function (Blueprint $table) {
                $table->dropColumn('helper');
            });
        }
    }
};
