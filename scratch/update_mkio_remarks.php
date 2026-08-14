<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$tst = \App\Models\TeamStockTransfer::where('transfer_number', 'TST-20260814-MKIO')->first();
if ($tst) {
    $tst->update([
        'remarks' => "[Marketing]: marketing>"
    ]);
    echo "Successfully updated TST-20260814-MKIO remarks to '[Marketing]: marketing>'!\n";
}
