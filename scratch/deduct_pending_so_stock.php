<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$undeducted = \App\Models\SalesOrder::with('items')->where(function($q) {
    $q->where('stock_deducted', false)->orWhereNull('stock_deducted');
})->get();

echo "Found " . $undeducted->count() . " undeducted sales orders.\n";
foreach ($undeducted as $so) {
    echo "Processing SO #" . $so->so_number . " (Status: " . $so->status . ", Items: " . $so->items->count() . ")...\n";
    if ($so->items->count() > 0) {
        try {
            \App\Services\StockDeductionService::deductForSalesOrder($so);
            echo "Successfully deducted stock for SO #" . $so->so_number . "\n";
        } catch (\Exception $e) {
            echo "Error deducting stock for SO #" . $so->so_number . ": " . $e->getMessage() . "\n";
        }
    }
}
