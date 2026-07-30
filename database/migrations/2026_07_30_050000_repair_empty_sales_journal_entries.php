<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\JournalEntry;
use App\Models\JournalEntryItem;
use App\Models\SalesOrder;
use App\Models\ChartOfAccount;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Find any existing SALE journal entries that have 0 line items
        $emptyEntries = JournalEntry::where('entry_type', 'SALE')
            ->whereDoesntHave('items')
            ->get();

        foreach ($emptyEntries as $entry) {
            $order = SalesOrder::where('so_number', $entry->reference)->first();
            if (!$order) continue;

            $activeInvoice = null;
            if (in_array($order->type, ['area_consignment', 'area_sales_consignment'])) {
                $activeInvoice = \App\Models\SalesInvoice::where('so_id', $order->id)
                    ->where('status', '!=', 'cancelled')
                    ->latest()
                    ->first();
            }

            $totalAmount = $activeInvoice ? $activeInvoice->total_amount : $order->total_amount;
            if ($totalAmount <= 0) continue;

            // Resolve Accounts
            $arAccount = ChartOfAccount::where('code', '1200')
                ->orWhere('name', 'like', '%Accounts Receivable%')
                ->orWhere('name', 'like', '%Trade%Receivable%')
                ->first()
                ?? ChartOfAccount::firstOrCreate(
                    ['code' => '1200'],
                    ['name' => 'Trade/Accounts Receivable', 'type' => 'Asset', 'category' => 'Current Asset']
                );

            $cashAccount = ChartOfAccount::where('code', '1010')
                ->orWhere('code', '1000')
                ->orWhere('name', 'like', '%Cash%Bank%')
                ->orWhere('name', 'like', '%Cash%Hand%')
                ->first()
                ?? ChartOfAccount::firstOrCreate(
                    ['code' => '1000'],
                    ['name' => 'Cash in Bank', 'type' => 'Asset', 'category' => 'Current Asset']
                );

            $salesAccount = ChartOfAccount::where('code', '4000')
                ->orWhere('name', 'like', '%Sales%Books%')
                ->orWhere('name', 'like', '%Sales%')
                ->orWhere('name', 'like', '%Revenue%')
                ->first()
                ?? ChartOfAccount::firstOrCreate(
                    ['code' => '4000'],
                    ['name' => 'Sales - Books', 'type' => 'Income', 'category' => 'Revenue']
                );

            $debitAccount = ($order->type === 'calculator_pos' || $order->type === 'ecom_direct') ? $cashAccount : $arAccount;

            // Insert line items directly into the existing Journal Entry header (preserving Entry No, Date & Audit Trail)
            JournalEntryItem::create([
                'journal_entry_id' => $entry->id,
                'chart_of_account_id' => $debitAccount->id,
                'debit' => $totalAmount,
                'credit' => 0,
                'memo' => "Payment/Receivable for " . $order->so_number,
            ]);

            JournalEntryItem::create([
                'journal_entry_id' => $entry->id,
                'chart_of_account_id' => $salesAccount->id,
                'debit' => 0,
                'credit' => $totalAmount,
                'memo' => "Revenue recognition",
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // No rollback required
    }
};
