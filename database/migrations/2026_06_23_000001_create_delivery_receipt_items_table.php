<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeliveryReceiptItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('delivery_receipt_items')) {
            Schema::create('delivery_receipt_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('dr_id');
                $table->unsignedBigInteger('product_id')->nullable();
                $table->string('product_name')->nullable();
                $table->string('description')->nullable();
                $table->integer('quantity')->default(0);
                $table->decimal('unit_price', 12, 2)->default(0.00);
                $table->decimal('amount', 12, 2)->default(0.00);
                $table->timestamps();

                // Foreign keys
                $table->foreign('dr_id')->references('id')->on('delivery_receipts')->onDelete('cascade');
                $table->foreign('product_id')->references('id')->on('products')->onDelete('set null');

                // Indexes
                $table->index('dr_id');
                $table->index('product_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('delivery_receipt_items');
    }
}
