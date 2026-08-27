<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\TeamStockTransfer;
use App\Models\TeamStock;
use App\Models\Site;
use App\Models\SiteInventory;
use App\Models\InventoryTransaction;
use App\Models\Book;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('team_stock_transfers') || !Schema::hasTable('team_stocks')) {
            return;
        }

        $completedTransfers = TeamStockTransfer::with('items')
            ->where('status', 'completed')
            ->get();

        foreach ($completedTransfers as $transfer) {
            // Check if this transfer was already credited
            $alreadyCredited = InventoryTransaction::where('reference_number', $transfer->transfer_number)
                ->where('type', 'in')
                ->where('source', 'Team Stock Transfer')
                ->exists();

            if ($alreadyCredited) {
                continue;
            }

            // Resolve target site for team
            $teamName = trim($transfer->team_name);
            if (empty($teamName)) {
                continue;
            }

            $targetSite = Site::where('name', $teamName)
                ->orWhere('name', 'Site ' . $teamName)
                ->orWhere('code', strtolower(str_replace([' ', '-'], '_', $teamName)))
                ->orWhereRaw('LOWER(name) = ?', [strtolower($teamName)])
                ->orWhereRaw('LOWER(name) LIKE ?', ['%' . strtolower($teamName) . '%'])
                ->first();

            if (!$targetSite) {
                $targetSite = Site::create([
                    'name' => $teamName,
                    'code' => strtolower(str_replace([' ', '-'], '_', $teamName)),
                    'location' => 'Area Sales',
                    'description' => 'Area Sales ' . $teamName . ' Inventory',
                    'is_active' => true
                ]);
            }

            foreach ($transfer->items as $tItem) {
                $qty = (float)($tItem->packed_qty !== null && $tItem->packed_qty > 0 
                    ? $tItem->packed_qty 
                    : ($tItem->picked_qty !== null && $tItem->picked_qty > 0 ? $tItem->picked_qty : $tItem->quantity));

                if ($qty <= 0) {
                    continue;
                }

                // 1. Credit TeamStock balance
                $teamStock = TeamStock::firstOrNew([
                    'team_name' => $transfer->team_name,
                    'book_id' => $tItem->book_id,
                    'book_index_id' => $tItem->book_index_id,
                    'book_bundle_id' => $tItem->book_bundle_id,
                ]);
                $teamStock->quantity = ($teamStock->quantity ?? 0) + $qty;
                $teamStock->save();

                // 2. Sync SiteInventory
                $siteInv = SiteInventory::firstOrNew([
                    'site_id' => $targetSite->id,
                    'book_id' => $tItem->book_id,
                    'book_index_id' => $tItem->book_index_id,
                    'book_bundle_id' => $tItem->book_bundle_id,
                ]);
                $siteInv->quantity = ($siteInv->quantity ?? 0) + $qty;
                $siteInv->save();

                // 3. Record audit log
                if ($tItem->book_id) {
                    $book = Book::find($tItem->book_id);
                    InventoryTransaction::create([
                        'book_id'          => $tItem->book_id,
                        'type'             => 'in',
                        'quantity'         => $qty,
                        'location'         => $targetSite->name,
                        'source'           => 'Team Stock Transfer',
                        'reference_number' => $transfer->transfer_number,
                        'unit_cost'        => $book->cost ?? 0,
                        'total_cost'       => $qty * ($book->cost ?? 0),
                        'notes'            => 'Retroactive stock credit for ' . $transfer->transfer_number . ' to ' . $transfer->team_name,
                        'status'           => 'completed',
                        'transaction_date' => now(),
                        'user_id'          => 1,
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Safe no-op on rollback
    }
};
