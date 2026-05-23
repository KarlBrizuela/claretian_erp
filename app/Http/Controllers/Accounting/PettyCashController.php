<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\PettyCashVoucher;
use App\Models\ChartOfAccount;
use App\Services\AccountingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PettyCashController extends Controller
{
    protected $accounting;

    public function __construct(AccountingService $accounting)
    {
        $this->accounting = $accounting;
    }

    /**
     * Get the sidebar for the current user based on their division
     */
    protected function getUserSidebar()
    {
        $user = auth()->user();
        if (!$user) return 'admin-finance';

        $division = $user->division ?? 'Admin & Finance Division';
        
        if (strpos($division, 'Marketing') !== false) {
            return 'marketing';
        } elseif (strpos($division, 'Production') !== false) {
            return 'production';
        }
        
        return 'admin-finance';
    }

    public function index()
    {
        $vouchers = PettyCashVoucher::with('creator')
            ->withSum('items', 'amount')
            ->withCount('items')
            ->latest()
            ->paginate(15);

        return view('admin-finance.petty-cash.index', [
            'title' => 'Petty Cash Vouchers',
            'role' => 'Finance Manager',
            'sidebar' => $this->getUserSidebar(),
            'vouchers' => $vouchers
        ]);
    }

    public function create()
    {
        $expenseAccounts = ChartOfAccount::where('type', 'Expense')
            ->orWhereIn('code', ['6000', '6010', '6020', '6030'])
            ->orderBy('code')
            ->get();

        return view('admin-finance.petty-cash.create', [
            'title' => 'New Petty Cash Voucher',
            'role' => 'Finance Manager',
            'sidebar' => $this->getUserSidebar(),
            'expenseAccounts' => $expenseAccounts
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pcv_number' => 'required|unique:petty_cash_vouchers,pcv_number',
            'date' => 'required|date',
            'pay_to' => 'required|string',
            'approved_by' => 'nullable|string',
            'received_by' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.particulars' => 'required|string',
            'items.*.amount' => 'required|numeric|min:0',
        ]);

        try {
            DB::transaction(function () use ($validated) {
                $voucher = PettyCashVoucher::create([
                    'pcv_number' => $validated['pcv_number'],
                    'date'       => $validated['date'],
                    'pay_to'     => $validated['pay_to'],
                    'approved_by' => $validated['approved_by'] ?? null,
                    'received_by' => $validated['received_by'] ?? null,
                    'created_by' => auth()->id(),
                    'status'     => 'open',
                ]);

                foreach ($validated['items'] as $item) {
                    $voucher->items()->create($item);
                }
            });

            return redirect()->route('admin-finance.petty-cash.index')->with('success', 'Petty Cash Voucher created successfully.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Failed to create: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $voucher = PettyCashVoucher::with(['items.expenseAccount', 'creator'])->findOrFail($id);

        return view('admin-finance.petty-cash.show', [
            'title' => 'Petty Cash Voucher Details',
            'role' => 'Finance Manager',
            'sidebar' => $this->getUserSidebar(),
            'voucher' => $voucher
        ]);
    }

    public function summary(Request $request)
    {
        $month = $request->get('month', now()->format('Y-m'));
        
        $vouchers = PettyCashVoucher::where('date', 'like', "$month%")
            ->with(['items'])
            ->withSum('items', 'amount')
            ->get();

        return view('admin-finance.petty-cash.summary', [
            'title' => 'Petty Cash Summary (' . $month . ')',
            'role' => 'Finance Manager',
            'sidebar' => $this->getUserSidebar(),
            'vouchers' => $vouchers,
            'selectedMonth' => $month
        ]);
    }

    public function liquidate(Request $request)
    {
        $month = $request->input('month');
        $vouchers = PettyCashVoucher::where('status', 'open')
            ->where('date', 'like', "$month%")
            ->with('items.expenseAccount')
            ->get();

        if ($vouchers->isEmpty()) {
            return back()->with('error', 'No open vouchers found for this month to liquidate.');
        }

        try {
            DB::transaction(function () use ($vouchers, $month) {
                // Sum all items across all vouchers
                $totalAmount = $vouchers->flatMap->items->sum('amount');

                if ($totalAmount <= 0) {
                    throw new \Exception('Total amount must be greater than zero.');
                }

                // Post as a single petty cash expense line (code 6000 = Petty Cash Expenses)
                // AccountingService will fall back to first Expense account if 6000 doesn't exist
                $entry = $this->accounting->postPettyCashLiquidation([
                    'month'        => $month,
                    'expenses'     => ['6000' => $totalAmount],
                    'total_amount' => $totalAmount,
                    'memo'         => "Petty Cash Liquidation - " . $month,
                ]);

                // Mark all vouchers as liquidated
                foreach ($vouchers as $voucher) {
                    $voucher->update([
                        'status'           => 'liquidated',
                        'journal_entry_id' => $entry->id,
                    ]);
                }
            });

            return redirect()->route('admin-finance.petty-cash.index')->with('success', 'Petty Cash Vouchers liquidated and journal entry posted.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to liquidate: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $voucher = PettyCashVoucher::findOrFail($id);
        $voucher->items()->delete();
        $voucher->delete();

        return redirect()->route('admin-finance.petty-cash.index')->with('success', 'Petty Cash Voucher deleted successfully.');
    }
}
