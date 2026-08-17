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
        if (Schema::hasTable('sales_orders') && !Schema::hasColumn('sales_orders', 'currency')) {
            Schema::table('sales_orders', function (Blueprint $table) {
                $table->string('currency')->default('PHP')->nullable()->after('type');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('sales_orders') && Schema::hasColumn('sales_orders', 'currency')) {
            Schema::table('sales_orders', function (Blueprint $table) {
                $table->dropColumn('currency');
            });
        }
    }
};
