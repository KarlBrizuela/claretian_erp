<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Book;
use App\Models\InventoryTransaction;
use App\Models\ProductStock;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class InventoryController extends Controller
{
    public function overview()
    {
        $totalBooks = Book::count();
        $lowStock = Book::whereColumn('stock', '<=', 'reorder_point')->count();
        $outOfStock = Book::where('stock', 0)->count();
        $inventoryValue = Book::sum(DB::raw('stock * cost')); // Use cost for inventory value

        $books = Book::latest()->paginate(10);
        
        $recentMovements = InventoryTransaction::with('book')
            ->latest()
            ->limit(5)
            ->get();

        $totalMovements = InventoryTransaction::count();

        return view('production.inventory.overview', compact(
            'totalBooks', 
            'lowStock', 
            'outOfStock', 
            'inventoryValue',
            'books',
            'recentMovements',
            'totalMovements'
        ));
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
            'quantity' => 'nullable|integer|min:1',
            'new_stock' => 'nullable|integer|min:0',
        ]);

        try {
            DB::beginTransaction();

            $book = Book::findOrFail($bookId);
            
            // Get max_stock - default to a high value if not set
            $maxStock = $book->max_stock ?? PHP_INT_MAX;
            
            $oldStock = $book->stock;
            $newStock = $oldStock;

            if ($request->action === 'add') {
                $quantity = $request->quantity ?? 0;
                $newStock = $oldStock + $quantity;

                // Validate max stock constraint
                if ($newStock > $maxStock) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => "Cannot add stock. New total ({$newStock}) exceeds max stock ({$maxStock})"
                    ], 422);
                }
            } elseif ($request->action === 'set') {
                $newStock = $request->new_stock ?? $oldStock;

                // Validate max stock constraint
                if ($newStock > $maxStock) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => "Cannot set stock to {$newStock}. Max stock is {$maxStock}"
                    ], 422);
                }
            }

            // Update book stock
            $book->stock = $newStock;
            $book->save();

            // Create inventory transaction record
            $transaction = new InventoryTransaction();
            $transaction->book_id = $book->id;
            $transaction->type = $newStock > $oldStock ? 'in' : ($newStock < $oldStock ? 'out' : 'adjustment');
            $transaction->quantity = abs($newStock - $oldStock);
            $transaction->location = 'Main Warehouse';
            $transaction->source = 'Manual Adjustment';
            $transaction->notes = $request->action === 'add' 
                ? "Added {$request->quantity} units via inventory overview"
                : "Manually set stock from {$oldStock} to {$newStock}";
            $transaction->user_id = Auth::id();
            $transaction->transaction_date = now();
            $transaction->status = 'completed';
            $transaction->save();

            // Update ProductStock for location tracking
            $bookStock = ProductStock::firstOrNew([
                'book_id' => $book->id,
                'location' => 'Main Warehouse'
            ]);
            $bookStock->quantity = $newStock;
            $bookStock->save();

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
