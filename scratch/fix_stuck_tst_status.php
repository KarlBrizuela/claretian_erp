<?php
// Fix stuck team stock transfers with status='pending' (should be 'pending_picklist')
$transfers = \App\Models\TeamStockTransfer::where('status', 'pending')->get();
echo 'Found ' . $transfers->count() . ' transfers with status=pending:' . PHP_EOL;
foreach ($transfers as $t) {
    echo '  - ' . $t->transfer_number . ' (' . $t->team_name . ') status=' . $t->status . PHP_EOL;
}

if ($transfers->count() > 0) {
    \App\Models\TeamStockTransfer::where('status', 'pending')->update(['status' => 'pending_picklist']);
    echo PHP_EOL . 'Fixed! Updated ' . $transfers->count() . ' transfer(s) back to pending_picklist.' . PHP_EOL;
} else {
    echo 'No stuck transfers found.' . PHP_EOL;
    
    // Also check for 'in_progress' which was another wrong value from the old dropdown
    $inProgress = \App\Models\TeamStockTransfer::where('status', 'in_progress')->get();
    echo 'Found ' . $inProgress->count() . ' transfers with status=in_progress:' . PHP_EOL;
    foreach ($inProgress as $t) {
        echo '  - ' . $t->transfer_number . ' (' . $t->team_name . ')' . PHP_EOL;
    }
    if ($inProgress->count() > 0) {
        \App\Models\TeamStockTransfer::where('status', 'in_progress')->update(['status' => 'picking']);
        echo 'Fixed! Updated to picking.' . PHP_EOL;
    }
}
