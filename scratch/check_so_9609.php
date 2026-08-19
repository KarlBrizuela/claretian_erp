<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\SiteInventory;
use App\Models\TeamStock;
use App\Services\StockDeductionService;

StockDeductionService::syncTeamSitesInventory();

echo "=== TEAM STOCKS FOR TEAM A ===\n";
foreach (TeamStock::where('team_name', 'Team A')->get() as $ts) {
    echo "Book ID: {$ts->book_id} | Qty: {$ts->quantity}\n";
}

echo "\n=== SITE INVENTORIES FOR TEAM A SITE (Site ID: 21) ===\n";
foreach (SiteInventory::where('site_id', 21)->get() as $si) {
    echo "Book ID: {$si->book_id} | Qty: {$si->quantity}\n";
}
