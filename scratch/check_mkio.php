<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$tst = \App\Models\TeamStockTransfer::where('transfer_number', 'TST-20260814-MKIO')->first();
echo "=== RECORD FOR TST-20260814-MKIO ===\n";
if ($tst) {
    print_r($tst->toArray());
} else {
    echo "Not found! Latest:\n";
    print_r(\App\Models\TeamStockTransfer::latest()->first()->toArray());
}
