<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\SalesOrder;

$orders = SalesOrder::get();
foreach ($orders as $o) {
    echo "SO Number: {$o->so_number} - Type: {$o->type} - Total: {$o->total_amount} - Created At: {$o->created_at} - Proof of payment: '{$o->proof_of_payment}' - Payment Method: '{$o->payment_method}'\n";
}
