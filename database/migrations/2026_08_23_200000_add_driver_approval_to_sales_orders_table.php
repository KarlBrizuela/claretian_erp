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
            if (!Schema::hasColumn('sales_orders', 'driver_approval_status')) {
                $table->string('driver_approval_status')->nullable()->after('driver_id');
            }
            if (!Schema::hasColumn('sales_orders', 'driver_approved_by')) {
                $table->unsignedBigInteger('driver_approved_by')->nullable()->after('driver_approval_status');
                $table->foreign('driver_approved_by')->references('id')->on('users')->onDelete('set null');
            }
            if (!Schema::hasColumn('sales_orders', 'driver_approved_at')) {
                $table->timestamp('driver_approved_at')->nullable()->after('driver_approved_by');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_orders', function (Blueprint $table) {
            if (Schema::hasColumn('sales_orders', 'driver_approved_by')) {
                $table->dropForeign(['driver_approved_by']);
                $table->dropColumn('driver_approved_by');
            }
            if (Schema::hasColumn('sales_orders', 'driver_approval_status')) {
                $table->dropColumn('driver_approval_status');
            }
            if (Schema::hasColumn('sales_orders', 'driver_approved_at')) {
                $table->dropColumn('driver_approved_at');
            }
        });
    }
};
