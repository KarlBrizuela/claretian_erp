<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== COLUMNS IN BOOKS TABLE ===\n";
print_r(\Schema::getColumnListing('books'));

echo "\n=== SAMPLE BOOK RECORD ===\n";
$b = \App\Models\Book::first();
if ($b) {
    print_r($b->toArray());
}
