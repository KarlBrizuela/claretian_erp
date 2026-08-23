<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesReturnsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_returns', function (Blueprint $table) {
            $table->id();
            $table->string('return_no')->unique();
            $table->foreignId('sales_order_id')->constrained('sales_orders')->onDelete('cascade');
            $table->date('return_date');
            $table->decimal('refund_amount', 15, 2);
            $table->string('refund_method')->nullable();
            $table->boolean('inventory_restored')->default(false);
            $table->foreignId('journal_entry_id')->nullable()->constrained('journal_entries')->onDelete('set null');
            $table->text('remarks')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });

        Schema::create('sales_return_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_return_id')->constrained('sales_returns')->onDelete('cascade');
            $table->foreignId('sales_order_item_id')->constrained('sales_order_items')->onDelete('cascade');
            $table->foreignId('book_id')->nullable()->constrained('books')->onDelete('set null');
            $table->integer('returned_qty');
            $table->decimal('price', 15, 2);
            $table->decimal('subtotal', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sales_return_items');
        Schema::dropIfExists('sales_returns');
    }
}
