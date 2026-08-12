<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== COMPANIES & BRANCHES ===" . PHP_EOL;
foreach (\App\Models\Company::all() as $c) {
    echo "ID: {$c->company_id} | Name: {$c->company_name} | Acct: {$c->account_number} | ParentID: {$c->parent_id}" . PHP_EOL;
}

echo PHP_EOL . "=== SALES ORDERS (SO-NBS-) ===" . PHP_EOL;
foreach (\App\Models\SalesOrder::where('so_number', 'like', 'SO-NBS-%')->get() as $so) {
    echo "SO ID: {$so->id} | SO#: {$so->so_number} | CustomerID: {$so->customer_id} | Rep: {$so->customer_representative} | Remarks: {$so->remarks}" . PHP_EOL;
    if ($so->customer) {
        echo "   Customer: {$so->customer->customer_name} | Company: {$so->customer->company_name} | Acct: {$so->customer->account_number}" . PHP_EOL;
    }
}
