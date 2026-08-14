<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$tst = \App\Models\TeamStockTransfer::where('transfer_number', 'TST-20260814-AOL4')->first();
if ($tst) {
    $tst->update([
        'remarks' => '[Admin & Finance]: Approved and verified by Admin & Finance.'
    ]);
    echo "Updated TST-20260814-AOL4 remarks successfully!\n";
}
