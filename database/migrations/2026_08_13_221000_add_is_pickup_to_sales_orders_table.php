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
        if (!Schema::hasColumn('sales_orders', 'is_pickup')) {
            Schema::table('sales_orders', function (Blueprint $table) {
                $table->boolean('is_pickup')->default(false)->after('helper');
                $table->timestamp('picked_up_at')->nullable()->after('is_pickup');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('sales_orders', 'is_pickup')) {
            Schema::table('sales_orders', function (Blueprint $table) {
                $table->dropColumn(['is_pickup', 'picked_up_at']);
            });
        }
    }
};
