<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\SalesOrder;
use App\Services\StockDeductionService;
use App\Models\TeamStock;
use App\Models\SiteInventory;

// Find all cancelled or rejected SalesOrders that still have stock_deducted = true
$cancelledOrders = SalesOrder::where('status', 'cancelled')
    ->where('stock_deducted', true)
    ->get();

echo "Found " . $cancelledOrders->count() . " cancelled orders with stock_deducted = true.\n";

foreach ($cancelledOrders as $order) {
    echo "Restoring stock for cancelled SO: {$order->so_number} (ID: {$order->id})...\n";
    StockDeductionService::restoreForSalesOrder($order, 'Cancelled Order Restoration');
    $order->update(['stock_deducted' => false]);
    echo "Restored SO: {$order->so_number}!\n";
}

StockDeductionService::syncTeamSitesInventory();

echo "\n=== UPDATED TEAM A STOCKS ===\n";
foreach (TeamStock::where('team_name', 'Team A')->get() as $ts) {
    echo "Book ID: {$ts->book_id} | Qty: {$ts->quantity}\n";
}

echo "\n=== UPDATED SITE INVENTORY FOR TEAM A SITE (Site ID: 21) ===\n";
foreach (SiteInventory::where('site_id', 21)->get() as $si) {
    echo "Book ID: {$si->book_id} | Qty: {$si->quantity}\n";
}
