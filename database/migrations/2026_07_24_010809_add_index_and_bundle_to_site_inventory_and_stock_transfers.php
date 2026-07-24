<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddIndexAndBundleToSiteInventoryAndStockTransfers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Disable foreign keys temporarily to modify columns
        Schema::disableForeignKeyConstraints();

        // 1. Alter site_inventory table
        // Try dropping foreign keys / unique constraint that might exist
        try {
            Schema::table('site_inventory', function (Blueprint $table) {
                $table->dropForeign('site_inventory_book_id_foreign');
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('site_inventory', function (Blueprint $table) {
                $table->dropUnique('site_inventory_site_id_book_id_unique');
            });
        } catch (\Exception $e) {}

        // Make book_id nullable if it isn't
        try {
            DB::statement('ALTER TABLE site_inventory MODIFY book_id BIGINT UNSIGNED NULL');
        } catch (\Exception $e) {}

        // Add columns if they do not exist
        Schema::table('site_inventory', function (Blueprint $table) {
            if (!Schema::hasColumn('site_inventory', 'book_index_id')) {
                $table->foreignId('book_index_id')->nullable()->after('book_id')->constrained('book_indices')->onDelete('cascade');
            }
            if (!Schema::hasColumn('site_inventory', 'book_bundle_id')) {
                $table->foreignId('book_bundle_id')->nullable()->after('book_index_id')->constrained('book_bundles')->onDelete('cascade');
            }
        });

        // Add composite unique key if it doesn't exist
        try {
            Schema::table('site_inventory', function (Blueprint $table) {
                $table->unique(['site_id', 'book_id', 'book_index_id', 'book_bundle_id'], 'site_inv_unique_composite');
            });
        } catch (\Exception $e) {}

        // Re-add foreign key constraint for book_id if needed
        try {
            Schema::table('site_inventory', function (Blueprint $table) {
                $table->foreign('book_id')->references('id')->on('books')->onDelete('cascade');
            });
        } catch (\Exception $e) {}

        // Drop temporary indexes if they exist
        try {
            Schema::table('site_inventory', function (Blueprint $table) {
                $table->dropIndex('temp_site_id_idx');
            });
        } catch (\Exception $e) {}
        try {
            Schema::table('site_inventory', function (Blueprint $table) {
                $table->dropIndex('temp_book_id_idx');
            });
        } catch (\Exception $e) {}


        // 2. Alter stock_transfers table
        try {
            Schema::table('stock_transfers', function (Blueprint $table) {
                $table->dropForeign('stock_transfers_book_id_foreign');
            });
        } catch (\Exception $e) {}

        try {
            DB::statement('ALTER TABLE stock_transfers MODIFY book_id BIGINT UNSIGNED NULL');
        } catch (\Exception $e) {}

        Schema::table('stock_transfers', function (Blueprint $table) {
            if (!Schema::hasColumn('stock_transfers', 'book_index_id')) {
                $table->foreignId('book_index_id')->nullable()->after('book_id')->constrained('book_indices')->onDelete('restrict');
            }
            if (!Schema::hasColumn('stock_transfers', 'book_bundle_id')) {
                $table->foreignId('book_bundle_id')->nullable()->after('book_index_id')->constrained('book_bundles')->onDelete('restrict');
            }
        });

        try {
            Schema::table('stock_transfers', function (Blueprint $table) {
                $table->foreign('book_id')->references('id')->on('books')->onDelete('restrict');
            });
        } catch (\Exception $e) {}

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();

        // 1. Rollback site_inventory
        try {
            Schema::table('site_inventory', function (Blueprint $table) {
                $table->dropUnique('site_inv_unique_composite');
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('site_inventory', function (Blueprint $table) {
                $table->dropForeign(['book_index_id']);
                $table->dropColumn('book_index_id');
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('site_inventory', function (Blueprint $table) {
                $table->dropForeign(['book_bundle_id']);
                $table->dropColumn('book_bundle_id');
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('site_inventory', function (Blueprint $table) {
                $table->dropForeign('site_inventory_book_id_foreign');
            });
        } catch (\Exception $e) {}
        
        // Revert book_id to NOT NULL
        try {
            DB::statement('ALTER TABLE site_inventory MODIFY book_id BIGINT UNSIGNED NOT NULL');
        } catch (\Exception $e) {}

        try {
            Schema::table('site_inventory', function (Blueprint $table) {
                // Re-add old unique constraint and foreign key
                $table->unique(['site_id', 'book_id'], 'site_inventory_site_id_book_id_unique');
                $table->foreign('book_id')->references('id')->on('books')->onDelete('cascade');
            });
        } catch (\Exception $e) {}

        // 2. Rollback stock_transfers
        try {
            Schema::table('stock_transfers', function (Blueprint $table) {
                $table->dropForeign('stock_transfers_book_id_foreign');
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('stock_transfers', function (Blueprint $table) {
                $table->dropForeign(['book_index_id']);
                $table->dropColumn('book_index_id');
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('stock_transfers', function (Blueprint $table) {
                $table->dropForeign(['book_bundle_id']);
                $table->dropColumn('book_bundle_id');
            });
        } catch (\Exception $e) {}
        
        try {
            DB::statement('ALTER TABLE stock_transfers MODIFY book_id BIGINT UNSIGNED NOT NULL');
        } catch (\Exception $e) {}

        try {
            Schema::table('stock_transfers', function (Blueprint $table) {
                $table->foreign('book_id')->references('id')->on('books')->onDelete('restrict');
            });
        } catch (\Exception $e) {}

        Schema::enableForeignKeyConstraints();
    }
}
