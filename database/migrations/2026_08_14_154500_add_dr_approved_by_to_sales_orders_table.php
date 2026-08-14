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
        if (Schema::hasTable('sales_orders') && !Schema::hasColumn('sales_orders', 'dr_approved_by')) {
            Schema::table('sales_orders', function (Blueprint $table) {
                $table->unsignedBigInteger('dr_approved_by')->nullable()->after('dr_prepared_by');
                $table->timestamp('dr_approved_at')->nullable()->after('dr_prepared_at');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('sales_orders') && Schema::hasColumn('sales_orders', 'dr_approved_by')) {
            Schema::table('sales_orders', function (Blueprint $table) {
                $table->dropColumn(['dr_approved_by', 'dr_approved_at']);
            });
        }
    }
};
