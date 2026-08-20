<?php
// Fix existing ecom_direct orders (like ID 8 DI-ECOM-2026-0001) so they have status = 'picking' and an active PickList
$ecomOrders = \App\Models\SalesOrder::where('type', 'ecom_direct')->get();

foreach ($ecomOrders as $so) {
    echo "Processing SO ID {$so->id} ({$so->so_number})...\n";
    $so->update(['status' => 'picking']);

    $existingPickList = \App\Models\PickList::where('sales_order_id', $so->id)->first();
    if (!$existingPickList) {
        $pickList = \App\Models\PickList::create([
            'sales_order_id'   => $so->id,
            'pick_list_number' => 'PL-' . $so->so_number . '-' . date('YmdHis'),
            'status'           => 'in_progress',
            'prepared_by'      => $so->prepared_by ?? 1,
        ]);

        $so->load('items');
        foreach ($so->items as $item) {
            \App\Models\PickListItem::create([
                'pick_list_id'        => $pickList->id,
                'sales_order_item_id' => $item->id,
                'requested_qty'       => $item->quantity,
                'picked_qty'          => 0,
                'status'              => 'pending'
            ]);
        }
        echo " -> Created PickList {$pickList->pick_list_number} with " . $so->items->count() . " items!\n";
    } else {
        echo " -> PickList {$existingPickList->pick_list_number} already exists.\n";
    }
}
echo "Done!\n";
