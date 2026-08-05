<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBookIndexIdToSalesOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sales_order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('sales_order_items', 'book_index_id')) {
                $table->unsignedBigInteger('book_index_id')->nullable()->after('bundle_id');
                $table->foreign('book_index_id')->references('id')->on('book_indices')->onDelete('set null');
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
            if (Schema::hasColumn('sales_order_items', 'book_index_id')) {
                $table->dropForeign(['book_index_id']);
                $table->dropColumn('book_index_id');
            }
        });
    }
}
