<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== COMPANIES ===" . PHP_EOL;
foreach (\App\Models\Company::whereNull('parent_id')->get() as $c) {
    echo "Company ID: {$c->company_id} | Name: {$c->company_name} | Acct: {$c->account_number}" . PHP_EOL;
    foreach ($c->branches as $b) {
        echo "   -- Branch ID: {$b->company_id} | Name: {$b->company_name} | Acct: {$b->account_number}" . PHP_EOL;
    }
}

echo PHP_EOL . "=== CUSTOMERS ===" . PHP_EOL;
foreach (\App\Models\Customer::all() as $cust) {
    echo "Customer ID: {$cust->customer_id} | Name: {$cust->customer_name} | CompanyName: {$cust->company_name} | Acct: {$cust->account_number}" . PHP_EOL;
}
