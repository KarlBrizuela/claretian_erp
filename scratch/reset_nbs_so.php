<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$nbsOrders = \App\Models\SalesOrder::where('so_number', 'like', 'SO-NBS-%')->get();

foreach ($nbsOrders as $so) {
    // Delete any attached picklists for fresh testing
    $pickLists = \App\Models\PickList::where('sales_order_id', $so->id)->get();
    foreach ($pickLists as $pl) {
        \App\Models\PickListItem::where('pick_list_id', $pl->id)->delete();
        $pl->delete();
    }

    $so->update([
        'status'          => 'pending_mkt_approval',
        'approved_by_mkt' => null,
        'mkt_approved_at' => null,
    ]);
    echo "Reset SO #{$so->so_number} to pending_mkt_approval." . PHP_EOL;
}
