<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class MakeDescriptionNullableOnDeliveryReceiptItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('delivery_receipt_items', 'description')) {
            DB::statement('ALTER TABLE delivery_receipt_items MODIFY description VARCHAR(255) NULL');
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('delivery_receipt_items', 'description')) {
            DB::statement('ALTER TABLE delivery_receipt_items MODIFY description VARCHAR(255) NOT NULL');
        }
    }
}
