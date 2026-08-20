<?php
// Check ecom_direct orders in the database and their picklists
$ecomOrders = \App\Models\SalesOrder::where('type', 'ecom_direct')->get();
echo "Found " . $ecomOrders->count() . " ecom_direct orders:\n";

foreach ($ecomOrders as $so) {
    $pickLists = \App\Models\PickList::where('sales_order_id', $so->id)->get();
    echo " - ID: {$so->id} | SO: {$so->so_number} | Status: {$so->status} | Platform: {$so->ecom_platform} | PickLists: " . $pickLists->count() . "\n";
}
