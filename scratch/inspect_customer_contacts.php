<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== CUSTOMERS CONTACT & REPRESENTATIVES ===\n";
foreach (\App\Models\Customer::all() as $c) {
    echo "Customer ID: {$c->customer_id} | Name: {$c->customer_name}\n";
    echo "  Main Phone: '{$c->main_phone}' | Mobile: '{$c->mobile}' | Work Phone: '{$c->work_phone}' | Other: '{$c->other_contact}'\n";
    echo "  Representatives: " . json_encode($c->representatives) . "\n\n";
}
