<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== ALL CUSTOMERS ===\n";
foreach (\App\Models\Customer::all() as $c) {
    echo "ID: {$c->customer_id} | Name: '{$c->customer_name}' | CompanyName: '{$c->company_name}' | AccountNum: '{$c->account_number}'\n";
}

echo "\n=== ALL COMPANIES ===\n";
foreach (\App\Models\Company::all() as $co) {
    echo "ID: {$co->company_id} | Name: '{$co->company_name}' | Code: '{$co->company_code}' | ParentID: '{$co->parent_id}'\n";
}
