<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== COMPANIES IN DB ===" . PHP_EOL;
foreach (\App\Models\Company::whereNull('parent_id')->get() as $p) {
    echo "Parent ID: {$p->company_id} | Name: {$p->company_name}" . PHP_EOL;
    foreach ($p->branches as $b) {
        echo "   -- Branch ID: {$b->company_id} | Name: {$b->company_name} | Parent: {$b->parent->company_name}" . PHP_EOL;
    }
}

echo PHP_EOL . "=== SO #41 DYNAMIC RESOLUTION ===" . PHP_EOL;
$order = \App\Models\SalesOrder::find(41);
if ($order) {
    $branchName = $order->customer_representative;
    if (!$branchName && $order->remarks && str_contains($order->remarks, 'Branch:')) {
        preg_match('/Branch:\s*([^|\n\r]+)/', $order->remarks, $m);
        $branchName = trim($m[1] ?? '');
    }
    $bCompany = $branchName ? \App\Models\Company::where('company_name', $branchName)->first() : null;
    
    $parentCompanyName = $bCompany?->parent?->company_name 
        ?: ($bCompany?->company_name 
        ?: ($order->customer?->company_name 
        ?: ($order->customer?->customer_name ?? 'N/A')));

    echo "SO ID: {$order->id}" . PHP_EOL;
    echo "Branch Name: {$branchName}" . PHP_EOL;
    echo "Parent Company Name (Dynamic): {$parentCompanyName}" . PHP_EOL;
    echo "Branch Account No: " . ($bCompany?->account_number ?: $order->customer?->account_number) . PHP_EOL;
}
