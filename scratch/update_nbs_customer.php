<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Update Customer record 28 (or abacus customer) to have company_name = 'Intracode' and customer_name = 'abacus'
$cust = \App\Models\Customer::where('customer_name', 'abacus')
    ->orWhere('company_name', 'abacus')
    ->first();

if ($cust) {
    $cust->update([
        'company_name'  => 'Intracode',
        'customer_name' => 'abacus',
    ]);
    echo "Updated Customer #{$cust->customer_id} -> company_name: Intracode, customer_name: abacus" . PHP_EOL;
} else {
    echo "Abacus customer not found" . PHP_EOL;
}

// Update existing Sales Orders starting with SO-NBS- to have customer_representative = 'abacus' if empty
$soCount = \App\Models\SalesOrder::where('so_number', 'like', 'SO-NBS-%')->count();
foreach (\App\Models\SalesOrder::where('so_number', 'like', 'SO-NBS-%')->get() as $so) {
    if (empty($so->customer_representative)) {
        $so->update(['customer_representative' => 'abacus']);
    }
}
echo "Updated {$soCount} existing NBS Sales Orders with customer_representative = 'abacus'." . PHP_EOL;
