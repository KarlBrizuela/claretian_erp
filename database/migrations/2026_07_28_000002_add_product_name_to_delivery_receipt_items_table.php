<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProductNameToDeliveryReceiptItemsTable extends Migration
{
    public function up()
    {
        Schema::table('delivery_receipt_items', function (Blueprint $table) {
            if (!Schema::hasColumn('delivery_receipt_items', 'product_name')) {
                $table->string('product_name')->nullable()->after('product_id');
            }
        });
    }

    public function down()
    {
        Schema::table('delivery_receipt_items', function (Blueprint $table) {
            $table->dropColumn('product_name');
        });
    }
}
