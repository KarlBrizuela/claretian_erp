<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMissingColumnsToSalesOrderItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sales_order_items', function (Blueprint $table) {
            $table->string('isbn')->nullable()->after('product_id');
            $table->string('unit')->default('pcs')->after('isbn');
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
            $table->dropColumn(['isbn', 'unit']);
        });
    }
}
