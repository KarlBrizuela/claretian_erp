<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Book;
use App\Models\BookIndex;
use App\Models\BookBundle;
use App\Models\SiteInventory;
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

        // Get Main Warehouse specifically
        $mainWarehouse = Site::where('name', 'Main Warehouse')->first();

        // Auto-sync: ensure all indices/bundles with stock > 0 are in site_inventory for Main Warehouse
        if ($mainWarehouse) {
            // Sync BookIndex stocks
            BookIndex::where('stock', '>', 0)->each(function($idx) use ($mainWarehouse) {
                $existing = SiteInventory::where('site_id', $mainWarehouse->id)
                    ->where('book_index_id', $idx->id)->first();
                if (!$existing) {
                    SiteInventory::create([
                        'site_id'        => $mainWarehouse->id,
                        'book_index_id'  => $idx->id,
                        'book_id'        => null,
                        'book_bundle_id' => null,
                        'quantity'       => $idx->stock,
                    ]);
                }
            });

            // Sync BookBundle stocks
            BookBundle::where('stock', '>', 0)->each(function($bundle) use ($mainWarehouse) {
                $existing = SiteInventory::where('site_id', $mainWarehouse->id)
                    ->where('book_bundle_id', $bundle->id)->first();
                if (!$existing) {
                    SiteInventory::create([
                        'site_id'        => $mainWarehouse->id,
                        'book_bundle_id' => $bundle->id,
                        'book_id'        => null,
                        'book_index_id'  => null,
                        'quantity'       => $bundle->stock,
                    ]);
                }
            });
        }

        // Virtual category warehouses dedicated strictly to Master Inventory
        $masterCategoryWarehouseNames = [
            'Bookstore Warehouse',
            'Area Sales Warehouse',
            'Consignment Warehouse',
            'Reserved Warehouse',
            'Book Sale Warehouse',
            'E-commerce Warehouse',
            'Damaged Stock Warehouse',
            'Returned Stock Warehouse',
            'In Transit Warehouse',
        ];

        // Fetch physical sites with fresh inventory after any sync
        $sites = Site::where('is_active', true)
            ->whereNotIn('name', $masterCategoryWarehouseNames)
            ->with(['inventory' => function ($q) {
                $q->where('quantity', '>', 0)->with(['book', 'bookIndex.book', 'bookBundle']);
            }])
            ->get();

        // Get all books
        $allBooks = Book::all();
        
        $query = Book::where('is_book', true)->latest();
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('sku', 'like', '%' . $search . '%')
                  ->orWhere('author', 'like', '%' . $search . '%')
                  ->orWhere('publisher', 'like', '%' . $search . '%');
            });
        }
        $books = $query->paginate(10, ['*'], 'books_page')->withQueryString();

        $nonBooksQuery = Book::where('is_book', false)->latest();
        if (!empty($search)) {
            $nonBooksQuery->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('sku', 'like', '%' . $search . '%')
                  ->orWhere('author', 'like', '%' . $search . '%')
                  ->orWhere('publisher', 'like', '%' . $search . '%');
            });
        }
        $nonBooks = $nonBooksQuery->paginate(10, ['*'], 'nonbooks_page')->withQueryString();

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
            ->get();

        $totalMovements = $recentMovements->count();

        $user = Auth::user();
        $userApprovalDivision = StockTransfer::approvalDivisionForUser($user);
        $isTransferApprover = $user && (
            $user->isSuperAdmin()
            || str_contains(strtolower($user->position ?? ''), 'manager')
            || str_contains(strtolower($user->position ?? ''), 'supervisor')
        );

        $pendingTransfers = StockTransfer::whereIn('status', ['logistics_assignment', 'logistics_assigned', 'completed'])
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

        // Fetch book indices
        $indicesQuery = \App\Models\BookIndex::with('book')->latest();
        if (!empty($search)) {
            $indicesQuery->where(function($q) use ($search) {
                $q->where('index_value', 'like', '%' . $search . '%')
                  ->orWhereHas('book', function($bq) use ($search) {
                      $bq->where('name', 'like', '%' . $search . '%')
                         ->orWhere('sku', 'like', '%' . $search . '%');
                  });
            });
        }
        $indices = $indicesQuery->paginate(10, ['*'], 'indices_page')->withQueryString();

        // Fetch book bundles
        $bundlesQuery = \App\Models\BookBundle::with('books')->latest();
        if (!empty($search)) {
            $bundlesQuery->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('sku', 'like', '%' . $search . '%');
            });
        }
        $bundles = $bundlesQuery->paginate(10, ['*'], 'bundles_page')->withQueryString();

        return view('production.inventory.overview', compact(
            'totalBooks', 
            'lowStock', 
            'outOfStock', 
            'inventoryValue',
            'books',
            'nonBooks',
            'allBooks',
            'recentMovements',
            'totalMovements',
            'sites',
            'pendingTransfers',
            'mainWarehouse',
            'stockTransferWorkflow',
            'logisticsUsers',
            'isAccountingReviewer',
            'isLogisticsAssigner',
            'indices',
            'bundles'
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
            } elseif ($request->action === 'set') {
                $newStock = $request->new_stock ?? $oldStock;
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

    public function updateIndexStockDirectly(Request $request, $indexId)
    {
        $request->validate([
            'action' => 'required|in:add,set',
            'quantity' => 'nullable|integer|min:0',
            'new_stock' => 'nullable|integer|min:0',
        ]);

        try {
            DB::beginTransaction();

            $index = \App\Models\BookIndex::findOrFail($indexId);
            $mainWarehouse = Site::where('name', 'Main Warehouse')->first();
            $mainWarehouseId = $mainWarehouse ? $mainWarehouse->id : 1;

            $mainSiteInv = SiteInventory::where('site_id', $mainWarehouseId)
                ->where('book_index_id', $index->id)
                ->first();

            $oldStock = $mainSiteInv ? (int)$mainSiteInv->quantity : (int)$index->stock;
            $newStock = $oldStock;

            if ($request->action === 'add') {
                $quantity = $request->quantity ?? 0;
                $newStock = $oldStock + $quantity;
            } elseif ($request->action === 'set') {
                $newStock = $request->new_stock ?? $oldStock;
            }

            $diff = $newStock - $oldStock;
            $index->stock = $newStock;
            $index->save();

            if ($diff != 0 && $index->book) {
                $book = $index->book;
                $book->stock = max(0, $book->stock - $diff);
                $book->save(); // Triggers BookObserver to update master book site_inventory
            }

            // Sync with Main Warehouse site inventory for index
            $mainWarehouse = Site::where('name', 'Main Warehouse')->first();
            if ($mainWarehouse) {
                $siteInv = SiteInventory::where('site_id', $mainWarehouse->id)
                    ->where('book_index_id', $index->id)
                    ->first();
                if (!$siteInv) {
                    $siteInv = new SiteInventory();
                    $siteInv->site_id = $mainWarehouse->id;
                    $siteInv->book_index_id = $index->id;
                    $siteInv->book_id = null;
                    $siteInv->book_bundle_id = null;
                }
                $siteInv->quantity = $newStock;
                $siteInv->save();
            }

            // Create inventory transaction record for auditing
            $transaction = new InventoryTransaction();
            $transaction->book_id = $index->book_id;
            $transaction->type = $newStock > $oldStock ? 'in' : ($newStock < $oldStock ? 'out' : 'adjustment');
            $transaction->quantity = abs($newStock - $oldStock);
            $transaction->location = 'Main Warehouse';
            $transaction->source = 'Manual Index Adjustment';
            $transaction->notes = "Index: {$index->index_value}. " . ($request->action === 'add' 
                ? "Added {$request->quantity} units to Index Stock via inventory overview"
                : "Manually set Index Stock from {$oldStock} to {$newStock}");
            $transaction->user_id = Auth::id();
            $transaction->transaction_date = now();
            $transaction->status = 'completed';
            $transaction->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Index stock updated successfully',
                'old_stock' => $oldStock,
                'new_stock' => $newStock,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update index stock: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateBundleStockDirectly(Request $request, $bundleId)
    {
        $request->validate([
            'action' => 'required|in:add,set',
            'quantity' => 'nullable|integer|min:0',
            'new_stock' => 'nullable|integer|min:0',
        ]);

        try {
            DB::beginTransaction();

            $bundle = \App\Models\BookBundle::findOrFail($bundleId);
            $oldStock = $bundle->stock;
            $newStock = $oldStock;

            if ($request->action === 'add') {
                $quantity = $request->quantity ?? 0;
                $newStock = $oldStock + $quantity;
            } elseif ($request->action === 'set') {
                $newStock = $request->new_stock ?? $oldStock;
            }

            $bundle->stock = $newStock;
            $bundle->save();

            // Sync with Main Warehouse site inventory
            $mainWarehouse = Site::where('name', 'Main Warehouse')->first();
            if ($mainWarehouse) {
                $siteInv = SiteInventory::where('site_id', $mainWarehouse->id)
                    ->where('book_bundle_id', $bundle->id)
                    ->first();
                if (!$siteInv) {
                    $siteInv = new SiteInventory();
                    $siteInv->site_id = $mainWarehouse->id;
                    $siteInv->book_bundle_id = $bundle->id;
                    $siteInv->book_id = null;
                    $siteInv->book_index_id = null;
                }
                $siteInv->quantity = $newStock;
                $siteInv->save();
            }

            // Create inventory transaction record (book_id = null for bundle-level adjustment)
            $transaction = new InventoryTransaction();
            $transaction->book_id = null;
            $transaction->type = $newStock > $oldStock ? 'in' : ($newStock < $oldStock ? 'out' : 'adjustment');
            $transaction->quantity = abs($newStock - $oldStock);
            $transaction->location = 'Main Warehouse';
            $transaction->source = 'Manual Bundle Adjustment';
            $transaction->notes = "Bundle: {$bundle->name}. " . ($request->action === 'add' 
                ? "Added {$request->quantity} units to Bundle Stock via inventory overview"
                : "Manually set Bundle Stock from {$oldStock} to {$newStock}");
            $transaction->user_id = Auth::id();
            $transaction->transaction_date = now();
            $transaction->status = 'completed';
            $transaction->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Bundle stock updated successfully',
                'old_stock' => $oldStock,
                'new_stock' => $newStock,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update bundle stock: ' . $e->getMessage()
            ], 500);
        }
    }

    public function masterInventory(Request $request)
    {
        $categories = [
            'Raw Materials',
            'Finished Books',
            'Office Supplies',
            'Warehouse',
            'Bookstore',
            'Consignment',
            'Seasonals',
            'Imported Books',
            'Events',
            'Book Sales',
            'E-commerce',
        ];

        $rawMaterialSubcategories = [
            'Paper',
            'Ink',
            'Glue',
            'Packaging',
            'Other',
        ];

        $warehouseNames = [
            'Main Warehouse',
            'Bookstore Warehouse',
            'Area Sales Warehouse',
            'Consignment Warehouse',
            'Reserved Warehouse',
            'Book Sale Warehouse',
            'E-commerce Warehouse',
            'Damaged Stock Warehouse',
            'Returned Stock Warehouse',
            'In Transit Warehouse',
        ];

        // Ensure all 10 warehouses exist in Site table
        foreach ($warehouseNames as $name) {
            \App\Models\Site::firstOrCreate(
                ['name' => $name],
                [
                    'code' => 'WH-' . strtoupper(substr(str_replace(' ', '', $name), 0, 4)),
                    'description' => "{$name} Stock Storage",
                    'is_active' => true
                ]
            );
        }

        $warehouses = \App\Models\Site::whereIn('name', $warehouseNames)->get();

        $selectedCategory = $request->query('category', 'All');
        $selectedSubcategory = $request->query('subcategory', 'All');
        $search = $request->query('search');

        $query = \App\Models\InventoryCategoryItem::with(['warehouseStocks.site']);

        if ($selectedCategory && $selectedCategory !== 'All') {
            $query->where('category', $selectedCategory);
        }

        if ($selectedCategory === 'Raw Materials' && $selectedSubcategory && $selectedSubcategory !== 'All') {
            $query->where('subcategory', $selectedSubcategory);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $items = $query->orderBy('category')->orderBy('name')->get();

        // Calculate statistics
        $totalItems = $items->count();
        $totalStockUnits = 0;
        $totalValuation = 0;
        $lowStockCount = 0;
        $damagedStockCount = 0;

        $damagedWarehouse = $warehouses->where('name', 'Damaged Stock Warehouse')->first();
        $returnedWarehouse = $warehouses->where('name', 'Returned Stock Warehouse')->first();

        foreach ($items as $item) {
            $itemTotalStock = $item->warehouseStocks->sum('quantity');
            $totalStockUnits += $itemTotalStock;
            $totalValuation += ($itemTotalStock * (float)$item->unit_cost);

            if ($itemTotalStock <= $item->reorder_point) {
                $lowStockCount++;
            }

            if ($damagedWarehouse) {
                $damagedStockCount += $item->warehouseStocks->where('site_id', $damagedWarehouse->id)->sum('quantity');
            }
            if ($returnedWarehouse) {
                $damagedStockCount += $item->warehouseStocks->where('site_id', $returnedWarehouse->id)->sum('quantity');
            }
        }

        return view('production.inventory.master-inventory', [
            'title' => 'Production Master Inventory',
            'role' => auth()->user() ? auth()->user()->position : 'Staff',
            'sidebar' => 'production',
            'categories' => $categories,
            'rawMaterialSubcategories' => $rawMaterialSubcategories,
            'selectedCategory' => $selectedCategory,
            'selectedSubcategory' => $selectedSubcategory,
            'search' => $search,
            'warehouses' => $warehouses,
            'items' => $items,
            'metrics' => [
                'total_items' => $totalItems,
                'total_stock_units' => $totalStockUnits,
                'total_valuation' => $totalValuation,
                'low_stock_count' => $lowStockCount,
                'damaged_stock_count' => $damagedStockCount,
                'active_warehouses_count' => $warehouses->count(),
            ],
        ]);
    }

    public function storeInventoryCategoryItem(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string',
            'subcategory' => 'nullable|string',
            'unit_of_measure' => 'required|string|max:50',
            'unit_cost' => 'required|numeric|min:0',
            'reorder_point' => 'required|integer|min:0',
            'initial_warehouse_id' => 'required|exists:sites,id',
            'initial_stock' => 'required|integer|min:0',
            'description' => 'nullable|string',
        ]);

        $sku = 'INV-' . strtoupper(substr($request->category, 0, 3)) . '-' . rand(10000, 99999);

        $item = \App\Models\InventoryCategoryItem::create([
            'sku' => $sku,
            'name' => $request->name,
            'category' => $request->category,
            'subcategory' => $request->category === 'Raw Materials' ? ($request->subcategory ?: 'Other') : null,
            'unit_of_measure' => $request->unit_of_measure,
            'unit_cost' => $request->unit_cost,
            'reorder_point' => $request->reorder_point,
            'description' => $request->description,
        ]);

        if ($request->initial_stock > 0) {
            \App\Models\WarehouseStockBalance::create([
                'site_id' => $request->initial_warehouse_id,
                'inventory_category_item_id' => $item->id,
                'quantity' => $request->initial_stock,
            ]);
        }

        return redirect()->back()->with('success', "Inventory Item '{$item->name}' created successfully!");
    }

    public function transferWarehouseStock(Request $request)
    {
        $request->validate([
            'inventory_category_item_id' => 'required|exists:inventory_category_items,id',
            'from_site_id' => 'required|exists:sites,id',
            'to_site_id' => 'required|exists:sites,id|different:from_site_id',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        $fromStock = \App\Models\WarehouseStockBalance::where('site_id', $request->from_site_id)
            ->where('inventory_category_item_id', $request->inventory_category_item_id)
            ->first();

        if (!$fromStock || $fromStock->quantity < $request->quantity) {
            return redirect()->back()->with('error', 'Insufficient stock in source warehouse for transfer.');
        }

        $fromStock->quantity -= $request->quantity;
        $fromStock->save();

        $toStock = \App\Models\WarehouseStockBalance::firstOrCreate(
            [
                'site_id' => $request->to_site_id,
                'inventory_category_item_id' => $request->inventory_category_item_id,
            ],
            ['quantity' => 0]
        );

        $toStock->quantity += $request->quantity;
        $toStock->save();

        return redirect()->back()->with('success', 'Stock transferred between warehouses successfully!');
    }

    public function updateWarehouseStockDirectly(Request $request)
    {
        $request->validate([
            'inventory_category_item_id' => 'required|exists:inventory_category_items,id',
            'site_id' => 'required|exists:sites,id',
            'quantity' => 'required|integer|min:0',
        ]);

        $stock = \App\Models\WarehouseStockBalance::firstOrCreate(
            [
                'site_id' => $request->site_id,
                'inventory_category_item_id' => $request->inventory_category_item_id,
            ],
            ['quantity' => 0]
        );

        $stock->quantity = $request->quantity;
        $stock->save();

        return redirect()->back()->with('success', 'Warehouse stock balance updated!');
    }
}

