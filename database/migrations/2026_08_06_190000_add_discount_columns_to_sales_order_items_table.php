<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDiscountColumnsToSalesOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sales_order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('sales_order_items', 'discount_value')) {
                $table->decimal('discount_value', 15, 2)->default(0)->after('price');
            }
            if (!Schema::hasColumn('sales_order_items', 'discount_type')) {
                $table->string('discount_type', 20)->default('percentage')->after('discount_value');
            }
            if (!Schema::hasColumn('sales_order_items', 'discount_amount')) {
                $table->decimal('discount_amount', 15, 2)->default(0)->after('discount_type');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sales_order_items', function (Blueprint $table) {
            if (Schema::hasColumn('sales_order_items', 'discount_amount')) {
                $table->dropColumn('discount_amount');
            }
            if (Schema::hasColumn('sales_order_items', 'discount_type')) {
                $table->dropColumn('discount_type');
            }
            if (Schema::hasColumn('sales_order_items', 'discount_value')) {
                $table->dropColumn('discount_value');
            }
        });
    }
}
