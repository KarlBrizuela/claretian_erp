<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        DB::statement('ALTER TABLE sales_orders MODIFY customer_id BIGINT UNSIGNED NULL');

        Schema::table('sales_orders', function (Blueprint $table) {
            if (!Schema::hasColumn('sales_orders', 'area_sales_staff_id')) {
                $table->foreignId('area_sales_staff_id')
                    ->nullable()
                    ->after('customer_id')
                    ->constrained('users')
                    ->nullOnDelete();
            }
        });
    }

    public function down()
    {
        Schema::table('sales_orders', function (Blueprint $table) {
            if (Schema::hasColumn('sales_orders', 'area_sales_staff_id')) {
                $table->dropConstrainedForeignId('area_sales_staff_id');
            }
        });

        DB::statement('ALTER TABLE sales_orders MODIFY customer_id BIGINT UNSIGNED NOT NULL');
    }
};
