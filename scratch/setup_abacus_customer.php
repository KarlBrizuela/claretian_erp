<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$abacusCustomer = \App\Models\Customer::where('company_name', 'like', '%abacus%')
    ->orWhere('customer_name', 'like', '%abacus%')
    ->first();

if (!$abacusCustomer) {
    $abacusCustomer = \App\Models\Customer::create([
        'customer_name'  => 'abacus',
        'company_name'   => 'abacus',
        'account_number' => 'CUST-NBS-ABACUS',
        'customer_type'  => 'business',
    ]);
    echo "Created Abacus Customer with ID: " . $abacusCustomer->customer_id . "\n";
} else {
    echo "Found Abacus Customer with ID: " . $abacusCustomer->customer_id . "\n";
}

$updatedCount = \App\Models\SalesOrder::where('so_number', 'like', 'SO-NBS-%')
    ->where('customer_id', 1)
    ->update(['customer_id' => $abacusCustomer->customer_id]);

echo "Updated {$updatedCount} existing NBS Sales Orders to Customer ID {$abacusCustomer->customer_id}\n";
