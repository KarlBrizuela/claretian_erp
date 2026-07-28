<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeDiscountPercentageNullableOnSalesOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sales_orders', function (Blueprint $table) {
            if (Schema::hasColumn('sales_orders', 'discount_percentage')) {
                $table->decimal('discount_percentage', 5, 2)->nullable()->default(0)->change();
            }
            if (Schema::hasColumn('sales_orders', 'discount_amount')) {
                $table->decimal('discount_amount', 15, 2)->nullable()->default(0)->change();
            }
            if (Schema::hasColumn('sales_orders', 'withholding_tax_amount')) {
                $table->decimal('withholding_tax_amount', 15, 2)->nullable()->default(0)->change();
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
        Schema::table('sales_orders', function (Blueprint $table) {
            if (Schema::hasColumn('sales_orders', 'discount_percentage')) {
                $table->decimal('discount_percentage', 5, 2)->default(0)->change();
            }
            if (Schema::hasColumn('sales_orders', 'discount_amount')) {
                $table->decimal('discount_amount', 15, 2)->default(0)->change();
            }
            if (Schema::hasColumn('sales_orders', 'withholding_tax_amount')) {
                $table->decimal('withholding_tax_amount', 15, 2)->default(0)->change();
            }
        });
    }
}
