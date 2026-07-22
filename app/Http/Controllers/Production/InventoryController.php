<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Book;
use App\Models\InventoryTransaction;
use App\Models\ProductStock;
use App\Models\Site;
use App\Models\StockTransfer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class InventoryController extends Controller
{
    public function overview(Request $request)
    {
        $search = $request->input('search');

        // Fetch sites and stock transfers first
        $sites = Site::where('is_active', true)
            ->with(['inventory' => function ($q) {
                $q->where('quantity', '>', 0)->with('book');
            }])
            ->get();

        // Get Main Warehouse specifically
        $mainWarehouse = Site::where('name', 'Main Warehouse')->first();

        // Get all books
        $allBooks = Book::all();
        
        $query = Book::latest();
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('sku', 'like', '%' . $search . '%')
                  ->orWhere('author', 'like', '%' . $search . '%')
                  ->orWhere('publisher', 'like', '%' . $search . '%');
            });
        }
        $books = $query->paginate(10)->withQueryString();

        // Calculate statistics based on MAIN WAREHOUSE ONLY
        $totalBooks = 0;
        $lowStock = 0;
        $outOfStock = 0;
        $inventoryValue = 0;

        foreach ($allBooks as $book) {
            $mainWarehouseQuantity = 0;
            
            // Get quantity from Main Warehouse only
            if ($mainWarehouse) {
                $mainWarehouseQuantity = $mainWarehouse->inventory()
                    ->where('book_id', $book->id)
                    ->sum('quantity');
            }

            if ($mainWarehouseQuantity > 0) {
                $totalBooks++;
                $inventoryValue += $mainWarehouseQuantity * ($book->cost ?? 0);
                
                if ($mainWarehouseQuantity <= ($book->reorder_point ?? 0)) {
                    $lowStock++;
                }
            } else {
                $outOfStock++;
            }
        }

        $recentMovements = InventoryTransaction::with('book')
            ->latest()
            ->limit(5)
            ->get();

        $totalMovements = InventoryTransaction::count();

        $user = Auth::user();
        $userApprovalDivision = StockTransfer::approvalDivisionForUser($user);
        $isTransferApprover = $user && (
            $user->isSuperAdmin()
            || str_contains(strtolower($user->position ?? ''), 'manager')
            || str_contains(strtolower($user->position ?? ''), 'supervisor')
        );

        $pendingTransfers = StockTransfer::where('status', 'pending')
            ->with(['fromSite', 'toSite', 'book', 'createdBy'])
            ->when(!$user?->isSuperAdmin(), function ($query) use ($user, $userApprovalDivision, $isTransferApprover) {
                $query->where(function ($scope) use ($user, $userApprovalDivision, $isTransferApprover) {
                    $scope->where('created_by', $user->id);

                    if ($isTransferApprover) {
                        $scope->orWhere('approval_division', $userApprovalDivision);
                    }
                });
            })
            ->latest()
            ->get();

        $stockTransferWorkflow = StockTransfer::with([
                'fromSite',
                'toSite',
                'book',
                'createdBy',
                'approvedBy',
                'accountingReviewedBy',
                'logisticsAssignedTo',
                'logisticsAssignedBy',
                'completedBy',
            ])
            ->whereIn('status', [
                'pending',
                'accounting_review',
                'logistics_assignment',
                'logistics_assigned',
                'completed',
                'rejected',
            ])
            ->when(!$user?->isSuperAdmin(), function ($query) use ($user, $userApprovalDivision, $isTransferApprover) {
                $query->where(function ($scope) use ($user, $userApprovalDivision, $isTransferApprover) {
                    $scope->where('created_by', $user->id)
                        ->orWhere('logistics_assigned_to', $user->id);

                    if ($isTransferApprover) {
                        $scope->orWhere('approval_division', $userApprovalDivision);
                    }

                    if ($this->isAccountingReviewer($user)) {
                        $scope->orWhere('status', 'accounting_review');
                    }

                    if ($this->isLogisticsAssigner($user)) {
                        $scope->orWhereIn('status', ['logistics_assignment', 'logistics_assigned']);
                    }
                });
            })
            ->latest()
            ->get();

        $logisticsUsers = User::where('position', 'like', '%Logistic%')
            ->where('status', true)
            ->orderBy('first_name')
            ->get();

        $isAccountingReviewer = $this->isAccountingReviewer($user);
        $isLogisticsAssigner = $this->isLogisticsAssigner($user);

        return view('production.inventory.overview', compact(
            'totalBooks', 
            'lowStock', 
            'outOfStock', 
            'inventoryValue',
            'books',
            'allBooks',
            'recentMovements',
            'totalMovements',
            'sites',
            'pendingTransfers',
            'mainWarehouse',
            'stockTransferWorkflow',
            'logisticsUsers',
            'isAccountingReviewer',
            'isLogisticsAssigner'
        ));
    }

    private function isAccountingReviewer($user): bool
    {
        if (!$user) {
            return false;
        }

        if ($user->isSuperAdmin()) {
            return true;
        }

        $values = collect([$user->division, $user->department, $user->position])
            ->filter()
            ->map(fn ($value) => strtolower($value));

        return $values->contains(fn ($value) => str_contains($value, 'accounting')
            || str_contains($value, 'finance')
            || str_contains($value, 'admin & finance'));
    }

    private function isLogisticsAssigner($user): bool
    {
        if (!$user) {
            return false;
        }

        if ($user->isSuperAdmin()) {
            return true;
        }

        $position = strtolower($user->position ?? '');

        return str_contains($position, 'logistic')
            && (str_contains($position, 'manager') || str_contains($position, 'supervisor') || str_contains($position, 'senior'));
    }

    public function addStock()
    {
        $books = Book::all();
        
        // Fetch completed received items for dropdown selection
        $completedItems = InventoryTransaction::with('book')
            ->where('type', 'in')
            ->where('status', 'completed')
            ->latest()
            ->get();
        
        return view('production.inventory.add-stock', compact('books', 'completedItems'));
    }

    public function received()
    {
        $books = Book::all();

        $receivedItems = InventoryTransaction::with('book')
            ->where('type', 'in')
            ->latest()
            ->get();
            
        return view('production.inventory.received', compact('receivedItems', 'books'));
    }

    public function storeStock(Request $request)
    {
        $request->validate([
            'transaction_date' => 'required|date',
            'book_id' => 'nullable|exists:books,id',
            'new_book_name' => 'nullable|string|max:255',
            'new_book_sku' => 'nullable|string|max:100|unique:books,sku',
            'quantity' => 'required|integer|min:1',
            'total_cost' => 'required|numeric|min:0',
            'stockSource' => 'required|string',
            'status' => 'required|string',
        ]);

        try {
            DB::beginTransaction();

            if ($request->has('new_product_mode') && $request->new_product_mode == '1') {
                if (!$request->new_book_name || !$request->new_book_sku) {
                     return back()->with('error', 'Book Name and SKU are required.')->withInput();
                }

                $book = new Book();
                $book->name = $request->new_book_name;
                $book->sku = $request->new_book_sku;
                $book->stock = 0;
                $book->save();
            } else {
                 if (!$request->book_id) {
                    return back()->with('error', 'Please select a book.')->withInput();
                }
                $book = Book::findOrFail($request->book_id);
            }
            
            $transaction = new InventoryTransaction();
            $transaction->book_id = $book->id;
            $transaction->type = 'in';
            $transaction->quantity = $request->quantity;
            $transaction->location = 'Main Warehouse';
            $transaction->source = $request->stockSource;
            $transaction->supplier = $request->stockSource; 
            $transaction->reference_number = $request->reference_number;
            $transaction->notes = $request->notes ?? 'Received item - awaiting stock addition';
            $transaction->user_id = Auth::id();
            $transaction->transaction_date = $request->transaction_date;
            $transaction->status = $request->status ?? 'pending';
            $transaction->total_cost = $request->total_cost;
            $transaction->unit_cost = $request->quantity > 0 ? ($request->total_cost / $request->quantity) : 0;
            $transaction->save();
            
            DB::commit();

            return redirect()->route('production.inventory.received')
                ->with('success', 'Received item recorded successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to add stock: ' . $e->getMessage())->withInput();
        }
    }

    public function updateTransaction(Request $request, $id)
    {
        $request->validate([
            'reference_number' => 'nullable|string|max:255',
            'transaction_date' => 'required|date',
            'stockSource' => 'required|string',
            'quantity' => 'required|integer|min:1',
            'total_cost' => 'required|numeric|min:0',
            'status' => 'required|string|in:completed,pending,cancelled',
        ]);

        try {
            DB::beginTransaction();

            $transaction = InventoryTransaction::findOrFail($id);
            
            // NOTE: Stock is NOT adjusted here anymore
            // Only update transaction details
            $transaction->reference_number = $request->reference_number;
            $transaction->transaction_date = $request->transaction_date;
            $transaction->source = $request->stockSource;
            $transaction->supplier = $request->stockSource;
            $transaction->quantity = $request->quantity;
            $transaction->total_cost = $request->total_cost;
            $transaction->unit_cost = $request->quantity > 0 ? ($request->total_cost / $request->quantity) : 0;
            $transaction->status = $request->status;
            
            $transaction->save();

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Transaction updated successfully']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroyTransaction($id)
    {
        try {
            DB::beginTransaction();

            $transaction = InventoryTransaction::findOrFail($id);
            $book = Book::findOrFail($transaction->book_id);

            // Revert stock if it was a completed transaction
            if ($transaction->status === 'completed') {
                if ($transaction->type == 'in') {
                    $book->stock -= $transaction->quantity;
                } else {
                    $book->stock += $transaction->quantity;
                }
                $book->save();
            }

            $transaction->delete();
            DB::commit();
            
            if (request()->ajax()) {
                return response()->json(['success' => true]);
            }
            return back()->with('success', 'Transaction deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
             if (request()->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', 'Failed to delete transaction.');
        }
    }

    public function getProductDetails($id)
    {
        $book = Book::find($id);
        if (!$book) {
            return response()->json(['error' => 'Book not found'], 404);
        }

        return response()->json([
            'sku' => $book->sku,
            'current_stock' => $book->stock,
            'cost' => number_format($book->cost, 2),
        ]);
    }

    /**
     * Process Add Stock - This is the ONLY method that updates product stock
     */
    public function processAddStock(Request $request)
    {
        $request->validate([
            'transaction_id' => 'required|exists:inventory_transactions,id',
            'quantity' => 'required|integer|min:1',
        ]);

        try {
            DB::beginTransaction();

            $transaction = InventoryTransaction::findOrFail($request->transaction_id);
            $book = Book::findOrFail($transaction->book_id);

            // Update book stock
            $book->stock += $request->quantity;
            $book->save();

            // Optionally update ProductStock (location-wise) - renamed to Book link in migration
            $bookStock = ProductStock::firstOrNew([
                'book_id' => $book->id,
                'location' => 'Main Warehouse'
            ]);
            $bookStock->quantity += $request->quantity;
            $bookStock->save();

            DB::commit();

            return redirect()->route('production.inventory.overview')
                ->with('success', 'Stock added successfully! Master inventory updated.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to add stock: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Update Stock Directly - API endpoint for inventory overview
     * Prevents stock from exceeding max_stock value
     */
    public function updateStockDirectly(Request $request, $bookId)
    {
        $request->validate([
            'action' => 'required|in:add,set',
            'site_id' => 'required|exists:sites,id',
            'quantity' => 'nullable|integer|min:1',
            'new_stock' => 'nullable|integer|min:0',
        ]);

        try {
            DB::beginTransaction();

            $book = Book::findOrFail($bookId);
            $site = Site::findOrFail($request->site_id);
            
            // Get site inventory record
            $siteInventory = \App\Models\SiteInventory::firstOrCreate(
                [
                    'site_id' => $site->id,
                    'book_id' => $book->id
                ],
                [
                    'quantity' => 0
                ]
            );

            // Get max_stock - default to site's max stock, then book's max stock, then high value
            $maxStock = $siteInventory->max_stock ?? $book->max_stock ?? PHP_INT_MAX;
            
            $oldStock = $siteInventory->quantity;
            $newStock = $oldStock;

            if ($request->action === 'add') {
                $quantity = $request->quantity ?? 0;
                $newStock = $oldStock + $quantity;

                // Validate max stock constraint
                if ($newStock > $maxStock) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => "Cannot add stock. New total ({$newStock}) exceeds max stock ({$maxStock}) for site {$site->name}"
                    ], 422);
                }
            } elseif ($request->action === 'set') {
                $newStock = $request->new_stock ?? $oldStock;

                // Validate max stock constraint
                if ($newStock > $maxStock) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => "Cannot set stock to {$newStock}. Max stock is {$maxStock} for site {$site->name}"
                    ], 422);
                }
            }

            // Update site inventory
            $siteInventory->quantity = $newStock;
            $siteInventory->save();

            // If it is Main Warehouse (site_id = 1), also update book stock
            if ($site->id == 1 || $site->name == 'Main Warehouse') {
                $book->stock = $newStock;
                $book->save();
            }

            // Update ProductStock for compatibility
            $bookStock = ProductStock::firstOrNew([
                'book_id' => $book->id,
                'location' => $site->name
            ]);
            $bookStock->quantity = $newStock;
            $bookStock->save();

            // Create inventory transaction record
            $transaction = new InventoryTransaction();
            $transaction->book_id = $book->id;
            $transaction->type = $newStock > $oldStock ? 'in' : ($newStock < $oldStock ? 'out' : 'adjustment');
            $transaction->quantity = abs($newStock - $oldStock);
            $transaction->location = $site->name;
            $transaction->source = 'Manual Adjustment';
            $transaction->notes = $request->action === 'add' 
                ? "Added {$request->quantity} units to {$site->name} via inventory overview"
                : "Manually set stock at {$site->name} from {$oldStock} to {$newStock}";
            $transaction->user_id = Auth::id();
            $transaction->transaction_date = now();
            $transaction->status = 'completed';
            $transaction->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Stock updated successfully',
                'old_stock' => $oldStock,
                'new_stock' => $newStock,
                'max_stock' => $maxStock
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update stock: ' . $e->getMessage()
            ], 500);
        }
    }
}
