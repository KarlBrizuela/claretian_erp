<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add tracking columns to sales_order_items for evaluation orders
        Schema::table('sales_order_items', function (Blueprint $table) {
            $table->integer('sent_qty')->nullable()->default(0)->comment('Qty sent for evaluation');
            $table->integer('returned_qty')->nullable()->default(0)->comment('Qty returned from evaluation');
            $table->integer('selected_qty')->nullable()->default(0)->comment('Qty customer selected (alias for customer_selected_qty tracking)');
        });

        // Add item selection tracking to rider_collection
        Schema::table('rider_collections', function (Blueprint $table) {
            $table->json('items_selection')->nullable()->comment('JSON: {book_id: {sent_qty, purchased_qty, returned_qty, status}}');
            $table->timestamp('evaluation_completed_at')->nullable()->comment('When customer selected items');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_order_items', function (Blueprint $table) {
            $table->dropColumn(['sent_qty', 'returned_qty', 'selected_qty']);
        });

        Schema::table('rider_collections', function (Blueprint $table) {
            $table->dropColumn(['items_selection', 'evaluation_completed_at']);
        });
    }
};
