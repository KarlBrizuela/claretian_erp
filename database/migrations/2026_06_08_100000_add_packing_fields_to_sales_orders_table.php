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
        Schema::table('sales_orders', function (Blueprint $table) {
            // Add packing fields
            $table->string('packing_prepared_by')->nullable()->after('dr_prepared_by');
            $table->json('packing_data')->nullable()->after('packing_prepared_by');
            $table->timestamp('picked_at')->nullable()->after('dr_prepared_at');
            $table->unsignedBigInteger('picked_by')->nullable()->after('picked_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_orders', function (Blueprint $table) {
            $table->dropColumn(['packing_data', 'packing_prepared_by', 'picked_at', 'picked_by']);
        });
    }
};
