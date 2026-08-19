<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$transfers = App\Models\StockTransfer::where('id', 2)
    ->orWhere('batch_id', 'ST-00002')
    ->orWhere('batch_id', '2')
    ->get();

echo "Transfers count: " . $transfers->count() . "\n";
foreach ($transfers as $t) {
    echo "ID: {$t->id}, batch_id: {$t->batch_id}, item_name: {$t->item_name}, quantity: {$t->quantity}, item_id: {$t->item_id}, item_type: {$t->item_type}\n";
}
