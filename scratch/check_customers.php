<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== ALL CUSTOMERS IN DB ===" . PHP_EOL;
foreach (\App\Models\Customer::all() as $c) {
    echo "ID: {$c->customer_id} | Company: {$c->company_name} | Name: {$c->customer_name} | Acct: {$c->account_number}" . PHP_EOL;
}
