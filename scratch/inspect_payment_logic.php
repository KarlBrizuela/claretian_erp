<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\SalesOrder;
use App\Models\Customer;

$orders = SalesOrder::with('invoice')->get();
foreach ($orders as $so) {
    echo "SO #{$so->so_number} (ID: {$so->id}) | Customer: {$so->customer_id}\n";
    echo "  - SO payment_status: '{$so->payment_status}'\n";
    echo "  - SO proof_of_payment: '{$so->proof_of_payment}'\n";
    echo "  - SO type: '{$so->type}'\n";
    if ($so->invoice) {
        echo "  - SI #{$so->invoice->si_number} | status: '{$so->invoice->status}'\n";
    } else {
        echo "  - SI: None\n";
    }
}
