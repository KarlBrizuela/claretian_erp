<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== SALES_ORDERS COLUMNS ===\n";
$cols = \Schema::getColumnListing('sales_orders');
print_r(array_values(array_filter($cols, function($c) {
    return str_contains($c, 'dr_') || str_contains($c, 'approved') || str_contains($c, 'prepared');
})));
