<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Book;
use App\Models\SiteInventory;
use App\Models\Site;

$mainWarehouse = Site::where('name', 'Main Warehouse')->first();
$mainId = $mainWarehouse ? $mainWarehouse->id : 1;

$mismatches = 0;
$books = Book::all();

foreach ($books as $b) {
    $siteInv = SiteInventory::where('site_id', $mainId)
        ->where('book_id', $b->id)
        ->first();
        
    if ($siteInv && (int)$siteInv->quantity !== (int)$b->stock) {
        echo "MISMATCH Book ID {$b->id} ('{$b->name}'): Book stock = {$b->stock}, SiteInventory qty = {$siteInv->quantity}\n";
        $mismatches++;
        // Fix: sync site_inventory to book stock
        $siteInv->quantity = $b->stock;
        $siteInv->save();
    }
}

echo "Done! Total mismatches found and corrected: {$mismatches}\n";
