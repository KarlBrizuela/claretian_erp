<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$books = App\Models\Book::all();
echo "Books Inventory Master List:\n";
echo "---------------------------------------------------------\n";
foreach ($books as $b) {
    echo "ID: {$b->id}\n";
    echo "Title: {$b->name}\n";
    echo "  - Stock: {$b->stock} pcs\n";
    echo "  - Unit Cost (Production Cost): ₱" . number_format($b->cost ?? 0, 2) . "\n";
    echo "  - Unit Price (Selling Price): ₱" . number_format($b->price ?? 0, 2) . "\n";
    echo "  - Total Cost Valuation (Stock * Cost): ₱" . number_format($b->stock * ($b->cost ?? 0), 2) . "\n\n";
}
