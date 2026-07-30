<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class MakeBookIdNullableInSalesOrderItems extends Migration
{
    public function up()
    {
        DB::statement('ALTER TABLE sales_order_items MODIFY book_id BIGINT UNSIGNED NULL DEFAULT NULL');
    }

    public function down()
    {
        DB::statement('ALTER TABLE sales_order_items MODIFY book_id BIGINT UNSIGNED NOT NULL');
    }
}
