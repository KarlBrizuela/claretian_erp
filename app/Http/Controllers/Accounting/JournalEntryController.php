<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use App\Models\JournalEntryItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JournalEntryController extends Controller
{
    public function index()
    {
        $entries = JournalEntry::with('creator')->latest()->paginate(20);
        return view('accounting.journal.index', compact('entries'));
    }

    public function show($id)
    {
        $entry = JournalEntry::with(['creator', 'items.account'])->findOrFail($id);
        return view('accounting.journal.show', compact('entry'));
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $entry = JournalEntry::findOrFail($id);
            
            // Delete associated items first (handled by DB if cascade set, but safe to do here)
            JournalEntryItem::where('journal_entry_id', $entry->id)->delete();
            $entry->delete();
            
            DB::commit();
            return redirect()->route('accounting.journal.index')->with('success', 'Journal Entry deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('accounting.journal.index')->with('error', 'Error deleting entry: ' . $e->getMessage());
        }
    }

    public function create()
    {
        $accounts = ChartOfAccount::where('is_active', true)->orderBy('code')->get();
        
        // Generate next Entry No (JV-YEAR-SEQ)
        $year = now()->year;
        $prefix = "JV-{$year}";
        $lastEntry = JournalEntry::where('entry_no', 'like', "{$prefix}-%")->orderBy('entry_no', 'desc')->first();
        
        if ($lastEntry) {
            $lastSeq = (int) substr($lastEntry->entry_no, -4);
            $newSeq = str_pad($lastSeq + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newSeq = '0001';
        }
        $entryNo = "{$prefix}-{$newSeq}";

        // Fetch Customers and Suppliers names for GJE
        $customers = \App\Models\Customer::select('customer_name as name')->whereNotNull('customer_name')->get();
        $suppliers = \App\Models\Supplier::select('company_name as name')->whereNotNull('company_name')->get();
        $names = $customers->concat($suppliers)
            ->pluck('name')
            ->unique()
            ->sort()
            ->values()
            ->toArray();

        return view('accounting.journal.create', compact('accounts', 'entryNo', 'names'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'entry_no' => 'required|unique:journal_entries,entry_no',
            'date' => 'required|date',
            'reference' => 'nullable|string',
            'memo' => 'nullable|string',
            'items' => 'required|array|min:2',
            'items.*.account_id' => 'required|exists:chart_of_accounts,id',
            'items.*.debit' => 'nullable|numeric|min:0',
            'items.*.credit' => 'nullable|numeric|min:0',
            'items.*.memo' => 'nullable|string',
            'items.*.name' => 'nullable|string',
        ]);

        // Validate Balances
        $totalDebit = collect($request->items)->sum('debit');
        $totalCredit = collect($request->items)->sum('credit');

        if (abs($totalDebit - $totalCredit) > 0.01) {
            return back()->withInput()->with('error', 'The journal entry must balance. Total Debit: ' . number_format($totalDebit, 2) . ', Total Credit: ' . number_format($totalCredit, 2));
        }

        if ($totalDebit <= 0) {
            return back()->withInput()->with('error', 'Total amount must be greater than zero.');
        }

        try {
            DB::beginTransaction();

            $entry = JournalEntry::create([
                'entry_no' => $request->entry_no,
                'entry_type' => 'GJE',
                'date' => $request->date,
                'reference' => $request->reference,
                'memo' => $request->memo,
                'currency' => 'PHP',
                'exchange_rate' => 1.0000,
                'created_by' => auth()->id(),
                'status' => 'posted',
            ]);

            foreach ($request->items as $item) {
                if (($item['debit'] ?? 0) > 0 || ($item['credit'] ?? 0) > 0) {
                    JournalEntryItem::create([
                        'journal_entry_id' => $entry->id,
                        'chart_of_account_id' => $item['account_id'],
                        'debit' => $item['debit'] ?? 0,
                        'credit' => $item['credit'] ?? 0,
                        'memo' => $item['memo'] ?? null,
                        'name' => $item['name'] ?? null,
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('accounting.journal.index')->with('success', 'Journal Entry ' . $entry->entry_no . ' posted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error posting entry: ' . $e->getMessage());
        }
    }
}
