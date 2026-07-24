<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddIndexAndBundleToSiteInventoryAndStockTransfers extends Migration
{
    private function indexExists(string $table, string $index): bool
    {
        $database = DB::getDatabaseName();

        $result = DB::select(
            'SELECT COUNT(*) AS count
             FROM information_schema.statistics
             WHERE table_schema = ?
               AND table_name = ?
               AND index_name = ?',
            [$database, $table, $index]
        );

        return (int) $result[0]->count > 0;
    }

    private function dropIndexIfExists(string $table, string $index): void
    {
        try {
            if ($this->indexExists($table, $index)) {
                DB::statement(sprintf('ALTER TABLE `%s` DROP INDEX `%s`', $table, $index));
            }
        } catch (\Throwable $e) {
            // Ignore missing index errors so the migration remains idempotent on live databases.
        }
    }

    private function foreignKeyExists(string $table, string $foreignKey): bool
    {
        $database = DB::getDatabaseName();

        $result = DB::select(
            'SELECT COUNT(*) AS count
             FROM information_schema.KEY_COLUMN_USAGE
             WHERE TABLE_SCHEMA = ?
               AND TABLE_NAME = ?
               AND CONSTRAINT_NAME = ?',
            [$database, $table, $foreignKey]
        );

        return (int) $result[0]->count > 0;
    }

    private function dropForeignKeyIfExists(string $table, string $foreignKey): void
    {
        try {
            if ($this->foreignKeyExists($table, $foreignKey)) {
                DB::statement(sprintf('ALTER TABLE `%s` DROP FOREIGN KEY `%s`', $table, $foreignKey));
            }
        } catch (\Throwable $e) {
            // Ignore missing foreign key errors so the migration remains idempotent on live databases.
        }
    }

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
        // Drop existing constraints only when they actually exist on the current database.
        // NOTE: Drop foreign keys FIRST (they may depend on indexes)
        $this->dropForeignKeyIfExists('site_inventory', 'site_inventory_book_id_foreign');
        $this->dropForeignKeyIfExists('site_inventory', 'fk_site_inventory_book_id');
        $this->dropForeignKeyIfExists('site_inventory', 'fk_site_inventory_site_id');
        
        // Then drop indexes (now that foreign keys are gone)
        $this->dropIndexIfExists('site_inventory', 'site_inventory_site_id_book_id_unique');
        $this->dropIndexIfExists('site_inventory', 'unique_site_book');

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
        // Try multiple possible foreign key names
        $this->dropForeignKeyIfExists('stock_transfers', 'stock_transfers_book_id_foreign');
        $this->dropForeignKeyIfExists('stock_transfers', 'fk_stock_transfers_book_id');

        try {
            DB::statement('ALTER TABLE stock_transfers MODIFY book_id BIGINT UNSIGNED NULL');
        } catch (\Exception $e) {}

        Schema::table('stock_transfers', function (Blueprint $table) {
            if (!Schema::hasColumn('stock_transfers', 'book_index_id')) {
                $table->foreignId('book_index_id')->nullable()->after('book_id')->constrained('book_indices')->onDelete('set null');
            }
            if (!Schema::hasColumn('stock_transfers', 'book_bundle_id')) {
                $table->foreignId('book_bundle_id')->nullable()->after('book_index_id')->constrained('book_bundles')->onDelete('set null');
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
        // Drop indexes and foreign keys that may have been created
        $this->dropIndexIfExists('site_inventory', 'site_inv_unique_composite');
        $this->dropForeignKeyIfExists('site_inventory', 'fk_site_inventory_book_index_id');
        $this->dropForeignKeyIfExists('site_inventory', 'fk_site_inventory_book_bundle_id');

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
                $table->dropForeign('fk_site_inventory_site_id');
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
                $table->foreign('site_id')->references('id')->on('sites')->onDelete('cascade');
                $table->foreign('book_id')->references('id')->on('books')->onDelete('cascade');
            });
        } catch (\Exception $e) {}

        // 2. Rollback stock_transfers
        $this->dropForeignKeyIfExists('stock_transfers', 'stock_transfers_book_id_foreign');
        $this->dropForeignKeyIfExists('stock_transfers', 'fk_stock_transfers_book_id');

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
