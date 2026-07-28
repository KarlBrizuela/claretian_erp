<?php

namespace App\Http\Controllers;

use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Models\PaymentSetting;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\AccountingService;

class POSController extends Controller
{
    protected $accounting;

    public function __construct(AccountingService $accounting)
    {
        $this->accounting = $accounting;
    }
    /**
     * Process a POS order
     */
    public function processOrder(Request $request)
    {
        $validated = $request->validate([
            'customer_id'         => 'nullable|exists:customers,customer_id',
            'payment_method'      => 'required|in:cash,gcash,paymaya,card,bank,check',
            'payment_reference'   => 'required_unless:payment_method,cash',
            'cash_received'       => 'required_if:payment_method,cash|numeric|min:0',
            'items'               => 'required|array|min:1',
            'items.*.product_id'  => 'nullable|exists:books,id',
            'items.*.bundle_id'   => 'nullable|exists:book_bundles,id',
            'items.*.quantity'    => 'required|integer|min:1',
            'items.*.price'       => 'required|numeric|min:0',
            'subtotal'            => 'required|numeric|min:0',
            'tax'                 => 'required|numeric|min:0',
            'total'               => 'required|numeric|min:0',
            'discount_value'      => 'nullable|numeric|min:0',
            'discount_type'       => 'nullable|string|in:amount,percentage',
        ]);

        // Validate cash payment
        if ($validated['payment_method'] === 'cash') {
            if ($validated['cash_received'] < $validated['total']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient cash received'
                ], 422);
            }
        }

        // ── STOCK VALIDATION ────────────────────────────────────────────────
        $insufficientItems = [];

        foreach ($validated['items'] as $item) {
            $qty = (int) $item['quantity'];

            if (!empty($item['bundle_id'])) {
                // --- Bundle item ---
                $bundle = \App\Models\BookBundle::with(['books' => fn($q) => $q->withPivot('quantity')->withSum('inventory as stock', 'quantity')])
                            ->find($item['bundle_id']);

                if (!$bundle || $bundle->stock < $qty) {
                    $name = $bundle ? $bundle->name : "Bundle #{$item['bundle_id']}";
                    $avail = $bundle ? $bundle->stock : 0;
                    $insufficientItems[] = "$name (Bundle stock available: $avail, requested: $qty)";
                } else {
                    // Check each book inside the bundle
                    foreach ($bundle->books as $book) {
                        $need = $book->pivot->quantity * $qty;
                        if ($book->stock < $need) {
                            $insufficientItems[] = "{$book->name} in bundle {$bundle->name} (Available: {$book->stock} pcs, Needed: $need pcs)";
                        }
                    }
                }
            } else {
                // --- Regular book item ---
                $book = Book::withSum('inventory as stock', 'quantity')->find($item['product_id']);
                if (!$book || $book->stock < $qty) {
                    $bookName  = $book ? $book->name : "Product #{$item['product_id']}";
                    $available = $book ? $book->stock : 0;
                    $insufficientItems[] = "$bookName (Available: $available pcs, Requested: $qty pcs)";
                }
            }
        }

        if (!empty($insufficientItems)) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient stock: ' . implode('; ', $insufficientItems)
            ], 422);
        }
        // ────────────────────────────────────────────────────────────────────

        try {
            DB::beginTransaction();

            // Generate order number
            $posConfig   = \App\Models\PaymentSetting::getSetting('pos_config', ['orderPrefix' => 'POS']);
            $prefix      = $posConfig['orderPrefix'] ?? 'POS';
            $date        = now()->format('Ymd');
            $lastOrder   = SalesOrder::where('so_number', 'like', "{$prefix}-{$date}-%")
                            ->orderBy('so_number', 'desc')->first();
            $lastNumber  = $lastOrder ? (int) substr($lastOrder->so_number, -4) : 0;
            $orderNumber = "{$prefix}-{$date}-" . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);

            $changeAmount = $validated['payment_method'] === 'cash'
                ? $validated['cash_received'] - $validated['total']
                : null;

            $discountAmount = 0;
            $discountPercentage = 0;
            if (!empty($validated['discount_value']) && $validated['discount_value'] > 0) {
                $discountValue = (float) $validated['discount_value'];
                if ($validated['discount_type'] === 'percentage') {
                    $discountPercentage = $discountValue;
                    $discountAmount = $validated['subtotal'] * ($discountPercentage / 100);
                } else {
                    $discountAmount = $discountValue;
                }
            }

            // Create sales order header
            $order = SalesOrder::create([
                'customer_id'      => $validated['customer_id'] ?? null,
                'so_number'        => $orderNumber,
                'type'             => 'calculator_pos',
                'status'           => 'completed',
                'payment_method'   => $validated['payment_method'],
                'payment_reference'=> $validated['payment_reference'] ?? null,
                'cash_received'    => $validated['cash_received'] ?? null,
                'change_amount'    => $changeAmount,
                'total_amount'     => $validated['total'],
                'tax_amount'       => $validated['tax'],
                'discount_amount'  => $discountAmount,
                'discount_percentage' => $discountPercentage ?? 0,
                'prepared_by'      => auth()->id(),
                'approved_by_mkt'  => auth()->id(),
                'approved_by_acct' => auth()->id(),
                'mkt_approved_at'  => now(),
                'acct_approved_at' => now(),
            ]);

            // ── PROCESS EACH LINE ITEM ───────────────────────────────────────
            foreach ($validated['items'] as $item) {
                $qty = (int) $item['quantity'];

                if (!empty($item['bundle_id'])) {
                    // ── Bundle item ───────────────────────────────────────
                    $bundle = \App\Models\BookBundle::with(['books' => fn($q) => $q->withPivot('quantity')])
                                ->find($item['bundle_id']);

                    // 1. Save the line item (book_id is null for bundle lines)
                    SalesOrderItem::create([
                        'sales_order_id'      => $order->id,
                        'book_id'             => null,
                        'bundle_id'           => $bundle->id,
                        'quantity'            => $qty,
                        'price'               => $item['price'],
                        'subtotal'            => $qty * $item['price'],
                        'unit'                => 'pcs',
                        'source_price_at_sale'=> 0,
                    ]);

                    // 2. Decrement bundle stock
                    \App\Models\BookBundle::where('id', $bundle->id)->decrement('stock', $qty);

                    // 3. Decrement each book's stock and log inventory transactions
                    foreach ($bundle->books as $book) {
                        $bookQtyToDeduct = $book->pivot->quantity * $qty;

                        $bookModel = Book::find($book->id);
                        if ($bookModel) {
                            $bookModel->stock -= $bookQtyToDeduct;
                            $bookModel->save();
                        }

                        \App\Models\InventoryTransaction::create([
                            'book_id'          => $book->id,
                            'type'             => 'out',
                            'quantity'         => $bookQtyToDeduct,
                            'location'         => 'Main Warehouse',
                            'source'           => 'POS Bundle Sale',
                            'reference_number' => $orderNumber,
                            'unit_cost'        => $book->cost ?? 0,
                            'total_cost'       => $bookQtyToDeduct * ($book->cost ?? 0),
                            'notes'            => "Bundle \"{$bundle->name}\" × {$qty} — POS Order #{$orderNumber}",
                            'status'           => 'completed',
                            'transaction_date' => now(),
                            'user_id'          => auth()->id(),
                        ]);
                    }

                } else {
                    // ── Regular book item ─────────────────────────────────
                    $book = Book::find($item['product_id']);

                    SalesOrderItem::create([
                        'sales_order_id'      => $order->id,
                        'book_id'             => $item['product_id'],
                        'bundle_id'           => null,
                        'quantity'            => $qty,
                        'price'               => $item['price'],
                        'subtotal'            => $qty * $item['price'],
                        'unit'                => 'pcs',
                        'source_price_at_sale'=> $book ? $book->source_price : 0,
                    ]);

                    $bookModel = Book::find($item['product_id']);
                    if ($bookModel) {
                        $bookModel->stock -= $qty;
                        $bookModel->save();
                    }

                    if ($book) {
                        \App\Models\InventoryTransaction::create([
                            'book_id'          => $book->id,
                            'type'             => 'out',
                            'quantity'         => $qty,
                            'location'         => 'Main Warehouse',
                            'source'           => 'POS Calculator',
                            'reference_number' => $orderNumber,
                            'unit_cost'        => $book->cost ?? 0,
                            'total_cost'       => $qty * ($book->cost ?? 0),
                            'notes'            => 'POS Order #' . $orderNumber,
                            'status'           => 'completed',
                            'transaction_date' => now(),
                            'user_id'          => auth()->id(),
                        ]);
                    }
                }
            }
            // ────────────────────────────────────────────────────────────────

            // Accounting integration
            $this->accounting->postSalesOrderEntry($order);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order processed successfully',
                'order'   => [
                    'id'             => $order->id,
                    'order_number'   => $orderNumber,
                    'total'          => $validated['total'],
                    'payment_method' => $validated['payment_method'],
                    'change'         => $changeAmount,
                    'created_at'     => $order->created_at->format('Y-m-d h:i A')
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to process order: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get POS order history
     */
    public function getOrders(Request $request)
    {
        $orders = SalesOrder::with(['customer', 'preparedBy'])
            ->where('type', 'calculator_pos')
            ->latest()
            ->paginate(20);

        return response()->json($orders);
    }

    /**
     * Get specific order details
     */
    public function getOrderDetails($id)
    {
        $order = SalesOrder::with(['customer', 'items.book', 'preparedBy'])
            ->where('type', 'calculator_pos')
            ->findOrFail($id);

        return response()->json($order);
    }

    /**
     * Get payment settings for POS
     */
    public function getPaymentSettings()
    {
        $settings = PaymentSetting::getAllSettings();
        
        return response()->json([
            'success' => true,
            'settings' => $settings
        ]);
    }

    /**
     * Save payment settings (for super admin)
     */
    public function savePaymentSettings(Request $request)
    {
        $validated = $request->validate([
            'gcash' => 'nullable|array',
            'gcash.number' => 'nullable|string',
            'gcash.qr' => 'nullable|string',
            'paymaya' => 'nullable|array',
            'paymaya.number' => 'nullable|string',
            'paymaya.qr' => 'nullable|string',
            'bank' => 'nullable|array',
            'bank.name' => 'nullable|string',
            'bank.accountName' => 'nullable|string',
            'bank.accountNumber' => 'nullable|string',
            'bank.qr' => 'nullable|string',
            'pos_config' => 'nullable|array',
            'pos_config.taxRate' => 'nullable|numeric',
            'pos_config.currencySymbol' => 'nullable|string',
            'pos_config.orderPrefix' => 'nullable|string',
        ]);

        try {
            foreach ($validated as $key => $value) {
                if ($value !== null) {
                    PaymentSetting::setSetting($key, $value);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Payment settings saved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save settings: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process E-commerce POS order
     */
    public function processEcomOrder(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,customer_id',
            'platform' => 'required|in:lazada,shopee,tiktok,website,facebook,other',
            'payment_method' => 'required|in:cod,gcash,lazada,shopee,paymaya,card,bank,check',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:books,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'subtotal' => 'required|numeric|min:0',
            'tax' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
            'payment_reference' => 'nullable|string',
            'discount_value' => 'nullable|numeric|min:0',
            'discount_type' => 'nullable|string|in:amount,percentage',
        ]);

        // STOCK VALIDATION: Check if all items have sufficient stock
        $insufficientItems = [];
        foreach ($validated['items'] as $item) {
            $book = Book::withSum('inventory as stock', 'quantity')->find($item['product_id']);
            if (!$book || $book->stock < $item['quantity']) {
                $bookName = $book ? $book->name : "Product #{$item['product_id']}";
                $availableStock = $book ? $book->stock : 0;
                $insufficientItems[] = "$bookName (Available: $availableStock pcs, Requested: {$item['quantity']} pcs)";
            }
        }

        if (!empty($insufficientItems)) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient stock for items: ' . implode(', ', $insufficientItems)
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Generate order number for E-com
            $date = now()->format('Ymd');
            $prefix = 'ECOM'; // Separate prefix for E-com
            
            // Get the last order number for today with this prefix
            $lastOrder = SalesOrder::where('so_number', 'like', "{$prefix}-{$date}-%")
                ->orderBy('so_number', 'desc')
                ->first();
            
            if ($lastOrder) {
                $lastNumber = (int) substr($lastOrder->so_number, -4);
                $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
            } else {
                $newNumber = '0001';
            }
            
            $orderNumber = "{$prefix}-{$date}-{$newNumber}";

            // Determine payment status
            // COD is unpaid, others are considered paid (or at least authorized)
            $paymentStatus = ($validated['payment_method'] === 'cod') ? 'unpaid' : 'paid';

            $discountAmount = 0;
            $discountPercentage = 0;
            if (!empty($validated['discount_value']) && $validated['discount_value'] > 0) {
                $discountValue = (float) $validated['discount_value'];
                if ($validated['discount_type'] === 'percentage') {
                    $discountPercentage = $discountValue;
                    $discountAmount = $validated['subtotal'] * ($discountPercentage / 100);
                } else {
                    $discountAmount = $discountValue;
                }
            }

            // Create sales order
            $order = SalesOrder::create([
                'customer_id' => $validated['customer_id'],
                'so_number' => $orderNumber,
                'type' => 'ecom_direct',
                'status' => 'completed', // Immediately completed for simplified flow, or use 'pending_delivery' if strict
                'platform' => $validated['platform'],
                'payment_method' => $validated['payment_method'],
                'payment_status' => $paymentStatus,
                'payment_reference' => $validated['payment_reference'] ?? null,
                'total_amount' => $validated['total'],
                'tax_amount' => $validated['tax'],
                'discount_amount' => $discountAmount,
                'discount_percentage' => $discountPercentage ?? 0,
                'remarks' => $validated['notes'] ?? null,
                'prepared_by' => auth()->id(),
                'approved_by_mkt' => auth()->id(),
                'approved_by_acct' => auth()->id(),
                'mkt_approved_at' => now(),
                'acct_approved_at' => now(),
            ]);

            // Determine platform site (Lazada, Shoppee, Tiktok)
            $platformStr = strtolower($validated['platform'] ?? '');
            $targetSite = null;
            if ($platformStr === 'lazada') {
                $targetSite = \App\Models\Site::whereRaw('LOWER(name) = ?', ['lazada'])->first();
            } elseif ($platformStr === 'shopee' || $platformStr === 'shoppee') {
                $targetSite = \App\Models\Site::whereRaw('LOWER(name) LIKE ?', ['%shop%'])->first();
            } elseif ($platformStr === 'tiktok') {
                $targetSite = \App\Models\Site::whereRaw('LOWER(name) LIKE ?', ['%tik%'])->first();
            }
            if (!$targetSite) {
                $targetSite = \App\Models\Site::where('name', 'Main Warehouse')->first();
            }

            // Create order items & deduct stock from specific platform site
            foreach ($validated['items'] as $item) {
                $book = Book::find($item['product_id']);
                SalesOrderItem::create([
                    'sales_order_id' => $order->id,
                    'book_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => $item['quantity'] * $item['price'],
                    'unit' => 'pcs',
                    'source_price_at_sale' => $book ? $book->source_price : 0,
                ]);

                // Decrement specific platform site inventory (Lazada, Shoppee, Tiktok)
                if ($targetSite && $book) {
                    $siteInv = \App\Models\SiteInventory::firstOrNew([
                        'site_id' => $targetSite->id,
                        'book_id' => $book->id
                    ]);
                    $siteInv->quantity = max(0, ($siteInv->quantity ?? 0) - $item['quantity']);
                    $siteInv->save();
                }

                // Decrement master book stock
                if ($book) {
                    $book->stock = max(0, ($book->stock ?? 0) - $item['quantity']);
                    $book->save();

                    // Record inventory transaction for platform site
                    \App\Models\InventoryTransaction::create([
                        'book_id' => $book->id,
                        'type' => 'out',
                        'quantity' => $item['quantity'],
                        'location' => $targetSite ? $targetSite->name : 'Main Warehouse',
                        'source' => 'E-com Direct',
                        'reference_number' => $orderNumber,
                        'unit_cost' => $book->cost ?? 0,
                        'total_cost' => $item['quantity'] * ($book->cost ?? 0),
                        'notes' => 'E-com Order #' . $orderNumber . ' - Platform: ' . ucfirst($validated['platform']),
                        'status' => 'completed',
                        'transaction_date' => now(),
                        'user_id' => auth()->id()
                    ]);
                }
            }

            // --- ACCOUNTING INTEGRATION ---
            $this->accounting->postSalesOrderEntry($order);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Online order processed successfully',
                'order' => [
                    'id' => $order->id,
                    'order_number' => $orderNumber,
                    'total' => $validated['total'],
                    'payment_status' => $paymentStatus,
                    'created_at' => $order->created_at->format('Y-m-d h:i A')
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to process order: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lookup product by barcode (for barcode scanner)
     */
    public function lookupByBarcode(Request $request)
    {
        $barcode = $request->input('barcode', '');
        
        if (!$barcode || strlen($barcode) < 3) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid barcode'
            ], 400);
        }

        $barcode = trim($barcode);

        // Search by barcode or SKU
        $product = Book::where('is_active', true)
            ->where(function($query) use ($barcode) {
                $query->where('barcode', $barcode)
                      ->orWhere('sku', $barcode)
                      ->orWhere('nbs_barcode', $barcode);
            })
            ->select('id', 'name', 'price', 'barcode', 'sku', 'category', 'image', 'is_book')
            ->first();

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'price' => (float)$product->price,
                'barcode' => $product->barcode,
                'sku' => $product->sku,
                'category' => $product->is_book ? 'books' : 'non-books',
                'image' => $product->image ? asset('storage/' . $product->image) : asset('images/no-book-cover.svg')
            ]
        ]);
    }
}
