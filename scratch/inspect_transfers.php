<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use App\Models\StockTransfer;

$transfers = StockTransfer::all();
foreach ($transfers as $t) {
    echo "ID: " . $t->id . " | Ref: ST-" . str_pad($t->id, 5, '0', STR_PAD_LEFT) . " | Status: " . $t->status . "\n";
}
