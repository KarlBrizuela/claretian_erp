<?php
// Test driver dashboard data fetching for Jimmuel Ante (ID 65)
$user = \App\Models\User::find(65);
echo "Testing Driver Dashboard query for Driver: {$user->first_name} {$user->last_name} (ID {$user->id})\n";

$driverId = $user->id;
$driverName = trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? ''));

$pickupQuery = \App\Models\PickupRequest::with('createdByUser')
    ->where(function($q) use ($driverId, $driverName, $user) {
        $q->where('driver_id', $driverId);
        if ($driverName) {
            $q->orWhere('driver_name', $driverName)
              ->orWhere('driver_name', 'like', '%' . ($user->first_name ?: $driverName) . '%');
        }
    });

$allPickupRequests = $pickupQuery->orderBy('requested_date', 'desc')->get();
echo "Found " . $allPickupRequests->count() . " Logistics Service Orders:\n";

foreach ($allPickupRequests as $pr) {
    echo " - REQ #{$pr->id} | Type: {$pr->type} | Client: {$pr->client_name} | DriverName: {$pr->driver_name} | Status: {$pr->status}\n";
}
