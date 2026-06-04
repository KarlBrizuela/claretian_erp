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
        Schema::table('inventory_transactions', function (Blueprint $table) {
            // Add relationships for evaluation tracking
            $table->unsignedBigInteger('sales_order_item_id')->nullable()->after('user_id')->comment('Link to SO item for evaluation');
            $table->unsignedBigInteger('rider_collection_id')->nullable()->after('sales_order_item_id')->comment('Link to rider collection');
            $table->unsignedBigInteger('related_transaction_id')->nullable()->after('rider_collection_id')->comment('Link to related transaction (e.g., return pairs)');

            // Add foreign keys
            $table->foreign('sales_order_item_id')->references('id')->on('sales_order_items')->onDelete('set null');
            $table->foreign('rider_collection_id')->references('id')->on('rider_collections')->onDelete('set null');
            $table->foreign('related_transaction_id')->references('id')->on('inventory_transactions')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory_transactions', function (Blueprint $table) {
            $table->dropForeign(['sales_order_item_id']);
            $table->dropForeign(['rider_collection_id']);
            $table->dropForeign(['related_transaction_id']);
            $table->dropColumn(['sales_order_item_id', 'rider_collection_id', 'related_transaction_id']);
        });
    }
};
