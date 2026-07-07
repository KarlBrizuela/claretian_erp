<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\OfficeSupply;
use App\Models\OfficeSupplyLog;
use App\Models\Supplier;
use Illuminate\Http\Request;

class OfficeSupplyController extends Controller
{
    /**
     * Display a listing of the office supplies.
     */
    public function index(Request $request)
    {
        // Permission check
        $user = auth()->user();
        if (!$user->isSuperAdmin() && !$user->hasPermission('admin_finance.accounting') && !$user->hasPermission('admin_finance.accounting.office_supplies')) {
            abort(403, 'Unauthorized.');
        }

        $search = $request->input('search');

        $supplies = OfficeSupply::when($search, function ($query, $search) {
            return $query->where('item_name', 'like', '%' . $search . '%');
        })
        ->orderBy('item_name', 'asc')
        ->paginate(15);

        // Fetch suppliers for dropdown
        $suppliers = Supplier::orderBy('company_name', 'asc')->get();

        // Fetch paginated transaction logs with filtering
        $logSearch = $request->input('log_search');
        $logStartDate = $request->input('log_start_date');
        $logEndDate = $request->input('log_end_date');

        $logsQuery = OfficeSupplyLog::with(['officeSupply', 'supplier', 'addedBy'])
            ->latest();

        if ($logSearch) {
            $logsQuery->whereHas('officeSupply', function ($query) use ($logSearch) {
                $query->where('item_name', 'like', '%' . $logSearch . '%');
            });
        }

        if ($logStartDate) {
            $logsQuery->whereDate('created_at', '>=', $logStartDate);
        }

        if ($logEndDate) {
            $logsQuery->whereDate('created_at', '<=', $logEndDate);
        }

        $logs = $logsQuery->paginate(10, ['*'], 'logs_page');

        return view('admin-finance.accounting.office-supplies.index', [
            'title' => 'Office Supplies Inventory',
            'role' => $user->position,
            'sidebar' => 'admin-finance',
            'supplies' => $supplies,
            'search' => $search,
            'suppliers' => $suppliers,
            'logs' => $logs,
            'log_search' => $logSearch,
            'log_start_date' => $logStartDate,
            'log_end_date' => $logEndDate
        ]);
    }

    /**
     * Store a newly created office supply in storage.
     */
    public function store(Request $request)
    {
        // Permission check
        $user = auth()->user();
        if (!$user->isSuperAdmin() && !$user->hasPermission('admin_finance.accounting') && !$user->hasPermission('admin_finance.accounting.office_supplies')) {
            abort(403, 'Unauthorized.');
        }

        $request->validate([
            'item_name' => 'required|string|max:255',
            'item_price' => 'required|numeric|min:0',
            'items_stock' => 'required|integer|min:0',
        ]);

        OfficeSupply::create([
            'item_name' => $request->item_name,
            'item_price' => $request->item_price,
            'items_stock' => $request->items_stock,
        ]);

        return redirect()->route('admin-finance.accounting.office-supplies.index')
            ->with('success', 'Office supply item created successfully.');
    }

    /**
     * Update the specified office supply in storage.
     */
    public function update(Request $request, $id)
    {
        // Permission check
        $user = auth()->user();
        if (!$user->isSuperAdmin() && !$user->hasPermission('admin_finance.accounting') && !$user->hasPermission('admin_finance.accounting.office_supplies')) {
            abort(403, 'Unauthorized.');
        }

        $request->validate([
            'item_name' => 'required|string|max:255',
            'item_price' => 'required|numeric|min:0',
            'items_stock' => 'required|integer|min:0',
        ]);

        $supply = OfficeSupply::findOrFail($id);
        $supply->update([
            'item_name' => $request->item_name,
            'item_price' => $request->item_price,
            'items_stock' => $request->items_stock,
        ]);

        return redirect()->route('admin-finance.accounting.office-supplies.index')
            ->with('success', 'Office supply item updated successfully.');
    }

    /**
     * Remove the specified office supply from storage.
     */
    public function destroy($id)
    {
        // Permission check
        $user = auth()->user();
        if (!$user->isSuperAdmin() && !$user->hasPermission('admin_finance.accounting') && !$user->hasPermission('admin_finance.accounting.office_supplies')) {
            abort(403, 'Unauthorized.');
        }

        $supply = OfficeSupply::findOrFail($id);
        $supply->delete();

        return redirect()->route('admin-finance.accounting.office-supplies.index')
            ->with('success', 'Office supply item deleted successfully.');
    }

    /**
     * Add stock to an office supply item.
     */
    public function addStock(Request $request, $id)
    {
        // Permission check
        $user = auth()->user();
        if (!$user->isSuperAdmin() && !$user->hasPermission('admin_finance.accounting') && !$user->hasPermission('admin_finance.accounting.office_supplies')) {
            abort(403, 'Unauthorized.');
        }

        $request->validate([
            'quantity' => 'required|integer|min:1',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'notes' => 'nullable|string|max:255',
        ]);

        $supply = OfficeSupply::findOrFail($id);

        $previousStock = $supply->items_stock;
        $quantity = (int) $request->quantity;
        $newStock = $previousStock + $quantity;

        // Update supply stock
        $supply->update([
            'items_stock' => $newStock,
        ]);

        // Create log entry
        OfficeSupplyLog::create([
            'office_supply_id' => $supply->id,
            'supplier_id' => $request->supplier_id,
            'added_by' => $user->id,
            'quantity' => $quantity,
            'previous_stock' => $previousStock,
            'new_stock' => $newStock,
            'notes' => $request->notes,
        ]);

        return redirect()->route('admin-finance.accounting.office-supplies.index')
            ->with('success', 'Stock of ' . $quantity . ' added successfully to ' . $supply->item_name . '.');
    }
}
