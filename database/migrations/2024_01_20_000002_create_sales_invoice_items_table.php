<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesInvoiceItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_invoice_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('si_id');
            $table->unsignedBigInteger('so_item_id')->nullable();
            $table->unsignedBigInteger('book_id');
            $table->integer('quantity')->default(0);
            $table->decimal('unit_price', 12, 2)->default(0);
            $table->decimal('amount', 12, 2)->default(0); // quantity * unit_price
            $table->timestamps();

            // Foreign keys
            $table->foreign('si_id')->references('id')->on('sales_invoices')->onDelete('cascade');
            $table->foreign('so_item_id')->references('id')->on('sales_order_items')->onDelete('set null');
            $table->foreign('book_id')->references('id')->on('books')->onDelete('restrict');

            // Indexes
            $table->index('si_id');
            $table->index('book_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sales_invoice_items');
    }
}
