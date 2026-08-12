<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$nbsOrders = \App\Models\SalesOrder::where('so_number', 'like', 'SO-NBS-%')->get();

foreach ($nbsOrders as $so) {
    $branchName = $so->customer_representative;
    if (!$branchName && $so->remarks && str_contains($so->remarks, 'Branch:')) {
        preg_match('/Branch:\s*([^|\n\r]+)/', $so->remarks, $m);
        $branchName = trim($m[1] ?? '');
    }

    if ($branchName) {
        $branchComp = \App\Models\Company::where('company_name', $branchName)->first();
        if ($branchComp && $branchComp->account_number && $so->customer) {
            $so->customer->update([
                'account_number' => $branchComp->account_number
            ]);
            echo "Updated Customer #{$so->customer->customer_id} account_number to {$branchComp->account_number} for Branch '{$branchName}'." . PHP_EOL;
        }
    }
}
