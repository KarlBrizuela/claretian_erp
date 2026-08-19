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
        if (!Schema::hasTable('consignment_inventories')) {
            Schema::create('consignment_inventories', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('sales_order_id')->nullable()->index();
                $table->unsignedBigInteger('customer_id')->nullable()->index();
                $table->string('team_name')->default('Main Warehouse')->index();
                $table->unsignedBigInteger('book_id')->nullable()->index();
                $table->unsignedBigInteger('book_index_id')->nullable()->index();
                $table->unsignedBigInteger('book_bundle_id')->nullable()->index();
                $table->integer('quantity')->default(0);
                $table->string('status')->default('consigned')->index(); // consigned, returned, invoiced, cancelled
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        if (Schema::hasTable('sales_orders') && !Schema::hasColumn('sales_orders', 'stock_deducted')) {
            Schema::table('sales_orders', function (Blueprint $table) {
                $table->boolean('stock_deducted')->default(false)->after('status');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consignment_inventories');
        if (Schema::hasTable('sales_orders') && Schema::hasColumn('sales_orders', 'stock_deducted')) {
            Schema::table('sales_orders', function (Blueprint $table) {
                $table->dropColumn('stock_deducted');
            });
        }
    }
};
