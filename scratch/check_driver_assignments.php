<?php
// Check user Jimmuel Ante and his assigned PickupRequests and SalesOrders
$user = \App\Models\User::where('first_name', 'like', '%Jimmuel%')
    ->orWhere('last_name', 'like', '%Ante%')
    ->first();

if (!$user) {
    echo "Jimmuel Ante user not found!\n";
    $allDrivers = \App\Models\User::where('position', 'Driver')->get();
    echo "All Drivers:\n";
    foreach ($allDrivers as $d) {
        echo " - ID: {$d->id}, Name: {$d->first_name} {$d->last_name}, email: {$d->email}\n";
    }
    exit;
}

echo "Found Driver: ID={$user->id}, Name={$user->first_name} {$user->last_name}, Position={$user->position}\n\n";

$pickupRequests = \App\Models\PickupRequest::where('driver_id', $user->id)->get();
echo "PickupRequests assigned to Jimmuel Ante (count=" . $pickupRequests->count() . "):\n";
foreach ($pickupRequests as $pr) {
    echo " - ID: {$pr->id}, Type: {$pr->type}, Client: {$pr->client_name}, DriverID: {$pr->driver_id}, DriverName: {$pr->driver_name}, Status: {$pr->status}, ReqDate: {$pr->requested_date}\n";
}

echo "\nAll PickupRequests in database (count=" . \App\Models\PickupRequest::count() . "):\n";
foreach (\App\Models\PickupRequest::all() as $pr) {
    echo " - ID: {$pr->id}, Type: {$pr->type}, Client: {$pr->client_name}, DriverID: {$pr->driver_id}, DriverName: '{$pr->driver_name}', Status: {$pr->status}, ReqDate: {$pr->requested_date}\n";
}

$salesOrders = \App\Models\SalesOrder::where('driver_id', $user->id)->get();
echo "\nSalesOrders assigned to Jimmuel Ante (count=" . $salesOrders->count() . "):\n";
foreach ($salesOrders as $so) {
    echo " - ID: {$so->id}, SO: {$so->so_number}, Status: {$so->status}, DeliveryDate: {$so->delivery_date}\n";
}
