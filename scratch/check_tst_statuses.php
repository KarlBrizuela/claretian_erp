<?php
// Check all team stock transfer statuses
$all = \App\Models\TeamStockTransfer::select('id', 'transfer_number', 'team_name', 'status', 'created_at')
    ->orderBy('id', 'desc')
    ->take(20)
    ->get();

echo 'Recent Team Stock Transfers:' . PHP_EOL;
echo str_pad('ID', 6) . str_pad('Transfer #', 25) . str_pad('Team', 15) . str_pad('Status', 20) . 'Created' . PHP_EOL;
echo str_repeat('-', 90) . PHP_EOL;
foreach ($all as $t) {
    echo str_pad($t->id, 6) . str_pad($t->transfer_number, 25) . str_pad($t->team_name, 15) . str_pad($t->status, 20) . $t->created_at . PHP_EOL;
}

echo PHP_EOL . 'Status distribution:' . PHP_EOL;
$statuses = \App\Models\TeamStockTransfer::selectRaw('status, count(*) as cnt')->groupBy('status')->get();
foreach ($statuses as $s) {
    echo '  ' . $s->status . ': ' . $s->cnt . PHP_EOL;
}

// Check for MIBF specifically
echo PHP_EOL . 'MIBF transfers:' . PHP_EOL;
$mibf = \App\Models\TeamStockTransfer::where('team_name', 'like', '%MIBF%')->orWhere('team_name', 'like', '%mibf%')->get();
foreach ($mibf as $t) {
    echo '  - ID: ' . $t->id . ' | ' . $t->transfer_number . ' | team: ' . $t->team_name . ' | status: ' . $t->status . PHP_EOL;
}
