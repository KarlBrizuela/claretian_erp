<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== COLUMNS IN TEAM_STOCK_TRANSFERS TABLE ===\n";
print_r(\Schema::getColumnListing('team_stock_transfers'));

echo "\n=== SAMPLE TEAM_STOCK_TRANSFER RECORD ===\n";
$tst = \App\Models\TeamStockTransfer::first();
if ($tst) {
    print_r($tst->toArray());
}
