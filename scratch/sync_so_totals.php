<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$updated = 0;
foreach (\App\Models\SalesOrder::with('items')->get() as $order) {
    $itemsSubtotal = $order->items->sum(function($item) {
        return ($item->subtotal !== null) ? (float)$item->subtotal : ((float)$item->quantity * (float)$item->price);
    });
    $discountAmount = (float) ($order->discount_amount ?? 0);
    $freightCharges = (float) ($order->freight_charges ?? 0);
    $serviceFee = $order->freight_option === 'freight_collect' ? 50.00 : 0;

    $calculatedTotal = max(0, $itemsSubtotal - $discountAmount + $freightCharges + $serviceFee);

    if (abs((float)$order->total_amount - $calculatedTotal) > 0.01) {
        $order->update(['total_amount' => $calculatedTotal]);
        $updated++;
    }
}

echo "Successfully synced $updated SalesOrders in DB.\n";
