<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Book;
use App\Models\BookIndex;
use App\Models\BookBundle;
use App\Models\SalesOrder;
use App\Models\StockTransfer;
use App\Models\TeamStockTransfer;
use App\Models\TeamStock;
use App\Models\SiteInventory;
use App\Models\InventoryTransaction;

echo "=== DATABASE INVENTORY STATS ===\n";
echo "Total Books: " . Book::count() . "\n";
echo "Total Book Indices: " . BookIndex::count() . "\n";
echo "Total Book Bundles: " . BookBundle::count() . "\n";
echo "Total Sales Orders: " . SalesOrder::count() . "\n";
echo "Total Site Stock Transfers: " . StockTransfer::count() . "\n";
echo "Total Team Stock Transfers: " . TeamStockTransfer::count() . "\n";
echo "Total Team Stock Records: " . TeamStock::count() . "\n";
echo "Total Site Inventory Records: " . SiteInventory::count() . "\n";
echo "Total Inventory Transactions: " . InventoryTransaction::count() . "\n";

echo "\n=== TEAM STOCK BALANCES == \n";
$teamStocks = TeamStock::with('book')->get();
if ($teamStocks->isEmpty()) {
    echo "No team stock balances found.\n";
} else {
    foreach ($teamStocks as $ts) {
        $name = $ts->book ? $ts->book->name : 'Item ID ' . ($ts->book_index_id ?: $ts->book_bundle_id);
        echo "Team: {$ts->team_name} | Book: {$name} | Qty: {$ts->quantity}\n";
    }
}

echo "\n=== SALES ORDERS SUMMARY ===\n";
$soList = SalesOrder::select('status', \DB::raw('count(*) as count'))->groupBy('status')->get();
foreach ($soList as $so) {
    echo "SO Status: {$so->status} | Count: {$so->count}\n";
}
