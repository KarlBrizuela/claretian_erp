<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFordFieldsToPurchaseOrderItemsTable extends Migration
{
    public function up()
    {
        Schema::table('purchase_order_items', function (Blueprint $table) {
            $table->string('item_remarks')->nullable()->after('total_amount');
            $table->string('language')->nullable()->after('item_remarks');
            $table->string('ft')->nullable()->after('language');
            $table->string('bindings')->nullable()->after('ft');
        });
    }

    public function down()
    {
        Schema::table('purchase_order_items', function (Blueprint $table) {
            $table->dropColumn(['language', 'ft', 'bindings', 'item_remarks']);
        });
    }
}
