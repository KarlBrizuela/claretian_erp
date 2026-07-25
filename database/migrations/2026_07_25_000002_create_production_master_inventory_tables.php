<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        // 1. Ensure the 10 production warehouses exist in sites table
        if (Schema::hasTable('sites')) {
            $requiredWarehouses = [
                ['name' => 'Main Warehouse', 'code' => 'WH-MAIN', 'description' => 'Main Production & Storage Warehouse'],
                ['name' => 'Bookstore Warehouse', 'code' => 'WH-BKS', 'description' => 'Bookstore Outlet & Retail Inventory'],
                ['name' => 'Area Sales Warehouse', 'code' => 'WH-ARS', 'description' => 'Area Sales Representatives Inventory'],
                ['name' => 'Consignment Warehouse', 'code' => 'WH-CSG', 'description' => 'Consignment Outlets & Partner Stock'],
                ['name' => 'Reserved Warehouse', 'code' => 'WH-RSV', 'description' => 'Reserved & Allocated Orders Stock'],
                ['name' => 'Book Sale Warehouse', 'code' => 'WH-BSL', 'description' => 'Book Fair & Promo Sales Stock'],
                ['name' => 'E-commerce Warehouse', 'code' => 'WH-ECM', 'description' => 'Online Platform & Fulfillment Inventory'],
                ['name' => 'Damaged Stock Warehouse', 'code' => 'WH-DMG', 'description' => 'Quarantined Damaged Stock'],
                ['name' => 'Returned Stock Warehouse', 'code' => 'WH-RET', 'description' => 'Customer Returned Stock'],
                ['name' => 'In Transit Warehouse', 'code' => 'WH-TRN', 'description' => 'Items Currently in Logistics Transit'],
            ];

            foreach ($requiredWarehouses as $wh) {
                $existing = DB::table('sites')->where('name', $wh['name'])->first();
                if (!$existing) {
                    DB::table('sites')->insert([
                        'name' => $wh['name'],
                        'code' => $wh['code'],
                        'description' => $wh['description'],
                        'location' => 'Main Facility',
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        // 2. Create inventory_category_items table
        if (!Schema::hasTable('inventory_category_items')) {
            Schema::create('inventory_category_items', function (Blueprint $table) {
                $table->id();
                $table->string('sku')->unique();
                $table->string('name');
                $table->string('category'); // Raw Materials, Finished Books, Office Supplies, Warehouse, Bookstore, Consignment, Seasonals, Imported Books, Events, Book Sales, E-commerce
                $table->string('subcategory')->nullable(); // Paper, Ink, Glue, Packaging, etc.
                $table->string('unit_of_measure')->default('pcs');
                $table->decimal('unit_cost', 15, 2)->default(0.00);
                $table->integer('reorder_point')->default(10);
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }

        // 3. Create warehouse_stock_balances table
        if (!Schema::hasTable('warehouse_stock_balances')) {
            Schema::create('warehouse_stock_balances', function (Blueprint $table) {
                $table->id();
                $table->foreignId('site_id')->constrained('sites')->onDelete('cascade');
                $table->foreignId('inventory_category_item_id')->constrained('inventory_category_items')->onDelete('cascade');
                $table->integer('quantity')->default(0);
                $table->timestamps();

                $table->unique(['site_id', 'inventory_category_item_id'], 'wh_item_unique');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouse_stock_balances');
        Schema::dropIfExists('inventory_category_items');
    }
};
