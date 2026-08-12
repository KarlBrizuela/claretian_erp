<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$parentCompanyName = "Abacus";
$defaultBranchName = "SM baguio";

$branchCompany = \App\Models\Company::where('company_name', $defaultBranchName)->first();
$accountNo = $branchCompany ? $branchCompany->account_number : ('CUST-NBS-' . strtoupper(\Illuminate\Support\Str::slug($defaultBranchName)));

$customer = \App\Models\Customer::where('account_number', $accountNo)
    ->orWhere('customer_name', 'like', "%{$defaultBranchName}%")
    ->orWhere('company_name', 'like', "%{$parentCompanyName}%")
    ->first();

if (!$customer) {
    $existingAcct = \App\Models\Customer::where('account_number', $accountNo)->first();
    if ($existingAcct) {
        $customer = $existingAcct;
        $customer->update([
            'company_name'   => $parentCompanyName,
            'customer_name'  => $defaultBranchName,
        ]);
    } else {
        $customer = \App\Models\Customer::create([
            'company_name'   => $parentCompanyName,
            'customer_name'  => $defaultBranchName,
            'account_number' => $accountNo,
            'customer_type'  => 'business',
        ]);
    }
} else {
    $customer->update([
        'company_name'   => $parentCompanyName,
        'customer_name'  => $defaultBranchName,
        'account_number' => $accountNo,
    ]);
}

echo "TEST SUCCESS: Customer ID {$customer->customer_id} | Company: {$customer->company_name} | Name: {$customer->customer_name} | Acct: {$customer->account_number}" . PHP_EOL;
