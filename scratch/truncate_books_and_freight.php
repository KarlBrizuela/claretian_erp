<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "========================================================\n";
echo "TRUNCATING BOOKS, INDICES, BUNDLES, AND FREIGHT QUOTATIONS\n";
echo "========================================================\n\n";

DB::statement('SET FOREIGN_KEY_CHECKS=0;');

$tablesToTruncate = [
    'books',
    'book_indices',
    'book_bundles',
    'book_bundle_items',
    'site_inventory',
    'site_inventories',
    'product_stocks',
    'team_stocks',
    'team_stock_transfers',
    'team_stock_transfer_items',
    'stock_transfers',
    'consignment_inventories',
    'inventory_transactions',
    'freight_quotations',
    'freight_bills',
    'freight_vouchers',
    'freight_voucher_items',
];

$truncatedCount = 0;
foreach ($tablesToTruncate as $table) {
    if (Schema::hasTable($table)) {
        DB::table($table)->truncate();
        echo "✓ Truncated table: {$table}\n";
        $truncatedCount++;
    } else {
        echo "- Table not found: {$table} (Skipped)\n";
    }
}

DB::statement('SET FOREIGN_KEY_CHECKS=1;');

echo "\n========================================================\n";
echo "SUCCESS: Truncated {$truncatedCount} tables cleanly!\n";
echo "========================================================\n";
