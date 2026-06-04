<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePickListItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pick_list_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pick_list_id')->constrained('pick_lists')->onDelete('cascade');
            $table->foreignId('sales_order_item_id')->constrained('sales_order_items')->onDelete('cascade');
            $table->decimal('requested_qty', 10, 2);
            $table->decimal('picked_qty', 10, 2)->default(0);
            $table->enum('status', ['pending', 'picked', 'short'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Index for querying by pick list
            $table->index('pick_list_id');
            $table->index('sales_order_item_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pick_list_items');
    }
}
