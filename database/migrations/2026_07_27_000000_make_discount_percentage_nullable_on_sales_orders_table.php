<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class MakeDiscountPercentageNullableOnSalesOrdersTable extends Migration
{
    public function up()
    {
        // Use raw SQL to avoid requiring doctrine/dbal for ->change()
        if (Schema::hasColumn('sales_orders', 'discount_percentage')) {
            DB::statement('ALTER TABLE sales_orders MODIFY discount_percentage DECIMAL(5,2) NULL DEFAULT 0');
        }
        if (Schema::hasColumn('sales_orders', 'discount_amount')) {
            DB::statement('ALTER TABLE sales_orders MODIFY discount_amount DECIMAL(15,2) NULL DEFAULT 0');
        }
        if (Schema::hasColumn('sales_orders', 'withholding_tax_amount')) {
            DB::statement('ALTER TABLE sales_orders MODIFY withholding_tax_amount DECIMAL(15,2) NULL DEFAULT 0');
        }
    }

    public function down()
    {
        if (Schema::hasColumn('sales_orders', 'discount_percentage')) {
            DB::statement('ALTER TABLE sales_orders MODIFY discount_percentage DECIMAL(5,2) NOT NULL DEFAULT 0');
        }
        if (Schema::hasColumn('sales_orders', 'discount_amount')) {
            DB::statement('ALTER TABLE sales_orders MODIFY discount_amount DECIMAL(15,2) NOT NULL DEFAULT 0');
        }
        if (Schema::hasColumn('sales_orders', 'withholding_tax_amount')) {
            DB::statement('ALTER TABLE sales_orders MODIFY withholding_tax_amount DECIMAL(15,2) NOT NULL DEFAULT 0');
        }
    }
}
