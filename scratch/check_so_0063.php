<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\FreightQuotation;
use App\Models\SalesOrder;

$so = SalesOrder::where('so_number', 'SO-2026-0063')->first();
if ($so) {
    echo "Found SalesOrder SO-2026-0063 (ID: {$so->id}): freight_charges={$so->freight_charges}, total_amount={$so->total_amount}\n";
    $fq = FreightQuotation::where('sales_order_id', $so->id)->orWhere('quote_number', 'SO-2026-0063')->get();
    foreach ($fq as $q) {
        echo "Found FreightQuotation ID: {$q->id}, quote_number: {$q->quote_number}, estimated_freight: {$q->estimated_freight}, valuation_percentage: {$q->valuation_percentage}, valuation_charge: {$q->valuation_charge}, handling_fee: {$q->handling_fee}, total_amount: {$q->total_amount}\n";
    }
} else {
    echo "SalesOrder SO-2026-0063 not found. Listing all FreightQuotations:\n";
    foreach (FreightQuotation::all() as $q) {
        echo "FreightQuotation ID: {$q->id}, quote_number: {$q->quote_number}, sales_order_id: {$q->sales_order_id}, estimated_freight: {$q->estimated_freight}, valuation_percentage: {$q->valuation_percentage}, valuation_charge: {$q->valuation_charge}, total_amount: {$q->total_amount}\n";
    }
}
