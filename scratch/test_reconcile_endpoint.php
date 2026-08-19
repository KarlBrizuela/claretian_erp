<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Book;
use App\Models\SiteInventory;
use App\Http\Controllers\Production\InventoryController;
use Illuminate\Http\Request;

// 1. Log in as user
$user = User::first();
auth()->login($user);

// 2. Create a test book with stock = 50 and intentionally set SiteInventory quantity = 20 (discrepancy)
$book = Book::create([
    'sku' => 'SKU-RECON-TEST-' . time(),
    'name' => 'Reconciliation Test Book',
    'stock' => 50,
    'price' => 100,
    'is_book' => true,
]);

SiteInventory::updateOrCreate(
    ['site_id' => 1, 'book_id' => $book->id],
    ['quantity' => 20]
);

echo "BEFORE RECONCILIATION:\n";
echo " - Book Stock: {$book->stock}\n";
echo " - SiteInventory Qty: " . SiteInventory::where('site_id', 1)->where('book_id', $book->id)->value('quantity') . "\n";

// 3. Call reconcileStock endpoint
$controller = app(InventoryController::class);
$request = Request::create('/production/inventory/reconcile-stock', 'POST');
$response = $controller->reconcileStock($request);

$responseData = json_decode($response->getContent(), true);

echo "\nENDPOINT RESPONSE:\n";
print_r($responseData);

// 4. Verify SiteInventory is now updated to 50
$updatedSiteInv = SiteInventory::where('site_id', 1)->where('book_id', $book->id)->value('quantity');

echo "\nAFTER RECONCILIATION:\n";
echo " - Book Stock: {$book->stock}\n";
echo " - SiteInventory Qty: {$updatedSiteInv}\n";

// Clean up test book
$book->delete();
SiteInventory::where('site_id', 1)->where('book_id', $book->id)->delete();

if ($responseData['success'] === true && (int)$updatedSiteInv === 50) {
    echo "\n>>> VERIFICATION SUCCESS: RECALCULATE & SYNC STOCK IS 100% WORKING! <<<\n";
} else {
    echo "\n>>> VERIFICATION FAILED <<<\n";
}
