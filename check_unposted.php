<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$orders = \App\Models\SalesOrder::where(function($q) {
    $q->whereNotNull('proof_of_payment')->where('proof_of_payment', '!=', '')
      ->orWhere('type', 'ecom_direct');
})->get();

echo "Orders with Proof of Payment or Ecom Direct:\n";
foreach ($orders as $o) {
    $entryExists = \App\Models\JournalEntry::where('reference', $o->so_number)->exists();
    echo "- SO: {$o->so_number}, Type: {$o->type}, Platform: {$o->ecom_platform}, Proof: {$o->proof_of_payment}, Total: {$o->total_amount}, Journal Entry Posted: " . ($entryExists ? 'YES' : 'NO') . "\n";
}
