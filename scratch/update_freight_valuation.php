<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\FreightQuotation;
use App\Models\SalesOrder;

echo "Updating Freight Quotations valuation charges...\n";

$quotations = FreightQuotation::all();

foreach ($quotations as $q) {
    echo "Processing Quote #{$q->quote_number} (ID: {$q->id})...\n";
    echo "  Before: Est Freight={$q->estimated_freight}, Valuation%={$q->valuation_percentage}, Valuation Charge={$q->valuation_charge}, Handling Fee={$q->handling_fee}, Total={$q->total_amount}\n";
    
    // Set valuation percentage to 0 and recalculate
    $estimatedFreight = (float) $q->estimated_freight;
    $handlingFee = (float) $q->handling_fee;
    $newValuationCharge = 0.00;
    $newTotal = $estimatedFreight + $newValuationCharge + $handlingFee;

    $q->update([
        'valuation_percentage' => 0,
        'valuation_charge' => 0,
        'total_amount' => $newTotal,
    ]);

    echo "  After: Est Freight={$q->estimated_freight}, Valuation%={$q->valuation_percentage}, Valuation Charge={$q->valuation_charge}, Handling Fee={$q->handling_fee}, Total={$q->total_amount}\n";

    if ($q->sales_order_id) {
        $so = SalesOrder::find($q->sales_order_id);
        if ($so) {
            $so->update([
                'freight_charges' => $newTotal,
            ]);
            $itemsSubtotal = $so->items()->sum('subtotal');
            $serviceFee = $q->freight_option === 'freight_collect' ? 50.00 : 0;
            $so->update([
                'total_amount' => $itemsSubtotal + $newTotal + $serviceFee,
            ]);
            echo "  Updated linked Sales Order #{$so->so_number} freight_charges={$newTotal}, total_amount={$so->total_amount}\n";
        }
    }
}

echo "Done!\n";
