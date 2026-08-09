<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$order = \App\Models\SalesOrder::with(['customer', 'preparedBy', 'drPreparedBy'])->latest()->first();
if ($order) {
    echo "Order ID: {$order->id}\n";
    echo "SO Number: {$order->so_number}\n";
    echo "Prepared By ID: {$order->prepared_by} | Name: " . ($order->preparedBy->name ?? 'NULL') . "\n";
    echo "DR Prepared By ID: {$order->dr_prepared_by} | Name: " . ($order->drPreparedBy->name ?? 'NULL') . "\n";
    echo "Customer Rep: '{$order->customer_representative}'\n";
    echo "Customer Name: '" . ($order->customer->customer_name ?? 'NULL') . "'\n";
}
