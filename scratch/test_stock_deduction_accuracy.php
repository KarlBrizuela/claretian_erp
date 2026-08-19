<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Book;
use App\Models\TeamStockTransfer;
use App\Models\TeamStockTransferItem;
use App\Models\TeamStock;
use App\Models\SiteInventory;
use App\Http\Controllers\Production\LogisticController;

// Mock login as super admin for authorization in delete method
$user = \App\Models\User::where('position', 'like', '%super%admin%')->first() ?: \App\Models\User::first();
auth()->login($user);

// 1. Pick a test book
$book = Book::where('is_book', true)->where('stock', '>=', 10)->first();
if (!$book) {
    die("No book with stock >= 10 found for testing.\n");
}

$initialBookStock = $book->stock;
echo "Starting test with Book ID {$book->id} ('{$book->name}'): Initial Stock = {$initialBookStock}\n";

// Ensure SiteInventory matches initial stock
$mainSiteInv = SiteInventory::where('site_id', 1)->where('book_id', $book->id)->first();
echo "Initial SiteInventory quantity = " . ($mainSiteInv ? $mainSiteInv->quantity : 'null') . "\n";

// 2. Create a dummy TeamStockTransfer for 1 unit
$transfer = TeamStockTransfer::create([
    'transfer_number' => 'TST-TEST-' . time(),
    'team_name' => 'MIBF',
    'transferred_by' => $user->id,
    'notes' => 'Test transfer accuracy',
    'status' => 'pending_picklist',
]);

$transferItem = TeamStockTransferItem::create([
    'team_stock_transfer_id' => $transfer->id,
    'book_id' => $book->id,
    'quantity' => 1,
]);

echo "Created TeamStockTransfer #{$transfer->transfer_number} for 1 unit.\n";

// 3. Run completeTeamStockPickList
$controller = app(LogisticController::class);
$controller->completeTeamStockPickList($transfer->id);

$book->refresh();
$mainSiteInv = SiteInventory::where('site_id', 1)->where('book_id', $book->id)->first();
$teamStock = TeamStock::where('team_name', 'MIBF')->where('book_id', $book->id)->first();

echo "AFTER PICK LIST COMPLETION:\n";
echo " - Book Stock: {$book->stock} (Expected: " . ($initialBookStock - 1) . ")\n";
echo " - SiteInventory Qty: " . ($mainSiteInv ? $mainSiteInv->quantity : 'null') . " (Expected: " . ($initialBookStock - 1) . ")\n";
echo " - TeamStock Qty for MIBF: " . ($teamStock ? $teamStock->quantity : 'null') . "\n";

$passPick = ($book->stock === $initialBookStock - 1) && ($mainSiteInv && $mainSiteInv->quantity === $initialBookStock - 1);

// 4. Now Delete the transfer to restore stock
$controller->deleteTeamStockTransfer($transfer->id);

$book->refresh();
$mainSiteInv = SiteInventory::where('site_id', 1)->where('book_id', $book->id)->first();
$teamStock = TeamStock::where('team_name', 'MIBF')->where('book_id', $book->id)->first();

echo "AFTER DELETION / RESTORATION:\n";
echo " - Book Stock: {$book->stock} (Expected: {$initialBookStock})\n";
echo " - SiteInventory Qty: " . ($mainSiteInv ? $mainSiteInv->quantity : 'null') . " (Expected: {$initialBookStock})\n";
echo " - TeamStock Qty for MIBF: " . ($teamStock ? $teamStock->quantity : '0') . "\n";

$passRestore = ($book->stock === $initialBookStock) && ($mainSiteInv && $mainSiteInv->quantity === $initialBookStock);

if ($passPick && $passRestore) {
    echo "\n>>> SUCCESS! STOCK DEDUCTION AND RESTORATION ARE 100% ACCURATE! <<<\n";
} else {
    echo "\n>>> FAILURE! Stock calculations are still off. <<<\n";
}
