<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Simulate getInventory for Main Warehouse (site_id=1)
$siteId = 1;
$inventory = \App\Models\SiteInventory::where('site_id', $siteId)
    ->with(['book', 'bookIndex.book', 'bookBundle'])
    ->where('quantity', '>', 0)
    ->get()
    ->map(function($item) {
        $type = 'book';
        $itemId = $item->book_id;
        $name = $item->book->name ?? 'Unknown';

        if ($item->book_index_id) {
            $type = 'index';
            $itemId = $item->book_index_id;
            $bookName = $item->bookIndex->book->name ?? 'Unknown';
            $name = $bookName . ' ' . ($item->bookIndex->index_value ?? '');
        } elseif ($item->book_bundle_id) {
            $type = 'bundle';
            $itemId = $item->book_bundle_id;
            $name = $item->bookBundle->name ?? 'Unknown Bundle';
        }

        return [
            'type'   => $type,
            'item_id'=> $itemId,
            'name'   => $name,
            'quantity'=> $item->quantity,
        ];
    });

echo "getInventory response for Main Warehouse:\n";
echo json_encode($inventory->values()->all(), JSON_PRETTY_PRINT) . "\n";
