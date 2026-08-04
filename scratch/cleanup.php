<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\SalesOrder;
use App\Models\Payment;

$so = SalesOrder::where('so_number', 'SO-2026-9946')->first();
if ($so) {
    Payment::where('sales_order_id', $so->id)->delete();
    $so->update(['payment_status' => 'unpaid']);
}

@unlink('scratch/check_col.php');
@unlink('scratch/test_partial_payment.php');
@unlink('scratch/test_full_payment_flow.php');
echo "Cleaned up successfully.\n";
