<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Book;
use App\Models\SiteInventory;
use App\Models\SalesOrder;
use App\Models\TeamStock;

echo "=== BOOKS ===\n";
foreach (Book::all() as $b) {
    echo "ID: {$b->id} | Name: {$b->name} | Stock: {$b->stock}\n";
}

echo "\n=== MAIN WAREHOUSE SITE INVENTORY (site_id = 1) ===\n";
$mainInventories = SiteInventory::where('site_id', 1)->get();
foreach ($mainInventories as $si) {
    echo "ID: {$si->id} | Book ID: {$si->book_id} | Index ID: {$si->book_index_id} | Qty: {$si->quantity}\n";
}

echo "\n=== TEAM STOCKS ===\n";
foreach (TeamStock::all() as $ts) {
    echo "Team: {$ts->team_name} | Book ID: {$ts->book_id} | Qty: {$ts->quantity}\n";
}

echo "\n=== RECENT SALES ORDERS (NBS / Consignment) ===\n";
$orders = SalesOrder::with('items')->latest()->take(5)->get();
foreach ($orders as $so) {
    echo "SO ID: {$so->id} | SO Num: {$so->so_number} | Type: {$so->type} | Stock Deducted: " . ($so->stock_deducted ? 'YES' : 'NO') . "\n";
    foreach ($so->items as $item) {
        echo "   -> Book ID: {$item->book_id} | Index ID: {$item->book_index_id} | Qty: {$item->quantity}\n";
    }
}
