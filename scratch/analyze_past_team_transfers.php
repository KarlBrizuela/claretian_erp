<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\TeamStockTransfer;
use App\Models\TeamStockTransferItem;
use App\Models\Book;

$transfers = TeamStockTransfer::with('items')->get();

echo "Total Team Stock Transfers found: " . $transfers->count() . "\n";
echo "---------------------------------------------------------\n";

foreach ($transfers as $t) {
    echo "Transfer #{$t->transfer_number} | Team: {$t->team_name} | Status: {$t->status} | Date: {$t->created_at}\n";
    foreach ($t->items as $item) {
        $name = $item->book ? $item->book->name : ($item->bookIndex ? $item->bookIndex->display_name : ($item->bookBundle ? $item->bookBundle->name : 'N/A'));
        echo "   -> Item ID: {$item->id} | Name: {$name} | Qty: {$item->quantity} | Picked: {$item->picked_qty} | Packed: {$item->packed_qty}\n";
    }
}
