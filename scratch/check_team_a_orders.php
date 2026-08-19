<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\SalesOrder;
use App\Models\TeamStock;
use App\Models\SiteInventory;
use App\Models\User;

echo "=== TEAM A USERS ===\n";
$teamAUserIds = User::where('sales_team', 'Team A')->pluck('id')->toArray();
echo "Team A User IDs: " . implode(', ', $teamAUserIds) . "\n\n";

echo "=== SALES ORDERS FOR TEAM A USERS ===\n";
$orders = SalesOrder::whereIn('prepared_by', $teamAUserIds)
    ->orWhereIn('area_sales_staff_id', $teamAUserIds)
    ->get();

foreach ($orders as $so) {
    echo "SO Num: {$so->so_number} | Status: {$so->status} | Type: {$so->type} | Deducted: " . ($so->stock_deducted ? 'YES' : 'NO') . "\n";
    foreach ($so->items as $item) {
        echo "   -> Book ID: {$item->book_id} | Index ID: {$item->book_index_id} | Qty: {$item->quantity}\n";
    }
}

echo "\n=== TEAM STOCKS FOR TEAM A ===\n";
foreach (TeamStock::where('team_name', 'Team A')->get() as $ts) {
    echo "Book ID: {$ts->book_id} | Index ID: {$ts->book_index_id} | Qty: {$ts->quantity}\n";
}

echo "\n=== SITE INVENTORY FOR TEAM A SITE (Site ID 21) ===\n";
foreach (SiteInventory::where('site_id', 21)->get() as $si) {
    echo "Book ID: {$si->book_id} | Index ID: {$si->book_index_id} | Qty: {$si->quantity}\n";
}
