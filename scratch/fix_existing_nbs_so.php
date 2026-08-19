<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\SalesOrder;
use App\Services\StockDeductionService;

$nbsOrders = SalesOrder::where('so_number', 'like', 'SO-NBS-%')->get();

foreach ($nbsOrders as $so) {
    echo "Processing SO: {$so->so_number} (ID: {$so->id})...\n";
    // Temporarily set stock_deducted to false so StockDeductionService runs fresh with updated logic
    $so->stock_deducted = false;
    $so->save();
    
    StockDeductionService::deductForSalesOrder($so);
    echo "Successfully deducted stock for SO: {$so->so_number}!\n";
}

// Re-verify stocks in DB
use App\Models\Book;
use App\Models\SiteInventory;

echo "\n=== UPDATED BOOKS IN MAIN WAREHOUSE ===\n";
foreach (Book::all() as $b) {
    $mainSi = SiteInventory::where('site_id', 1)->where('book_id', $b->id)->first();
    $mainQty = $mainSi ? $mainSi->quantity : 0;
    echo "Book ID: {$b->id} | Name: {$b->name} | Book Stock: {$b->stock} | Main WH SiteInventory Qty: {$mainQty}\n";
}
