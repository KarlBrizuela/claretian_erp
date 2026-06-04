<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\AccountingService;

class LogisticController extends Controller
{
    protected $accounting;

    public function __construct(AccountingService $accounting)
    {
        $this->accounting = $accounting;
    }
    public function pickListManagement(Request $request)
    {
        // Get existing pick lists (not completed)
        $pickLists = \App\Models\PickList::with(['salesOrder', 'salesOrder.customer', 'pickListItems.salesOrderItem.book', 'preparedByUser'])
            ->where('status', '!=', 'completed')
            ->latest()
            ->get();

        // Get completed pick lists for recreation option
        $completedPickLists = \App\Models\PickList::with(['salesOrder', 'salesOrder.customer', 'pickListItems.salesOrderItem.book', 'preparedByUser'])
            ->where('status', 'completed')
            ->latest()
            ->get();

        // Get pending Sales Orders ready for picking (status = 'picking' and no active pick list yet)
        $pendingOrders = \App\Models\SalesOrder::with('customer', 'items.book')
            ->where('status', 'picking')
            ->whereDoesntHave('pickLists', function($query) {
                $query->where('status', '!=', 'completed');
            })
            ->latest()
            ->get();

        // If pickListId is provided, preload that pick list
        $preloadPickListId = $request->input('pickListId');

        return view('production.logistic.pick-list-management', [
            'pickLists' => $pickLists,
            'completedPickLists' => $completedPickLists,
            'pendingOrders' => $pendingOrders,
            'preloadPickListId' => $preloadPickListId,
        ]);
    }

    public function pickListList()
    {
        $pickLists = \App\Models\PickList::with('salesOrder', 'salesOrder.customer', 'preparedByUser', 'pickListItems')
            ->where('status', '!=', 'completed')
            ->latest()
            ->get();

        \Illuminate\Support\Facades\Log::debug('PickListList pick lists count: ' . $pickLists->count());
        if ($pickLists->count() > 0) {
            \Illuminate\Support\Facades\Log::debug('First pick list: ' . $pickLists->first()->pick_list_number);
        }

        return view('production.logistic.pick-list-list', [
            'title' => 'Pick Lists',
            'role' => 'Logistics Staff',
            'sidebar' => 'production',
            'pickLists' => $pickLists
        ]);
    }

    public function showPickList($id)
    {
        $pickList = \App\Models\PickList::with(['salesOrder', 'salesOrder.customer', 'pickListItems.salesOrderItem.book', 'preparedByUser'])
            ->findOrFail($id);

        return view('production.logistic.pick-list-details', [
            'pickList' => $pickList
        ]);
    }

    public function markAsGathered(Request $request)
    {
        try {
            // Get order_id from request (JSON) or route parameter
            $orderId = $request->input('order_id') ?? $request->route('id');
            
            if (!$orderId) {
                return response()->json(['success' => false, 'message' => 'Order ID is required'], 400);
            }
            
            $order = \App\Models\SalesOrder::findOrFail($orderId);
            
            // Move to "pending_si_prep" status for Sales Invoice Preparation
            $order->update([
                'status' => 'pending_si_prep',
                'gathered_at' => now(),
                'gathered_by' => auth()->id()
            ]);

            // Mark all associated pick lists as completed
            \App\Models\PickList::where('sales_order_id', $orderId)
                ->where('status', '!=', 'completed')
                ->update([
                    'status' => 'completed',
                    'completed_at' => now(),
                    'completed_by' => auth()->id()
                ]);

            // Log the action
            \App\Models\ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'Pick list gathered',
                'description' => 'Order marked as gathered for SO ' . $order->so_number,
                'reference_type' => 'SalesOrder',
                'reference_id' => $order->id,
                'details' => json_encode(['gathered_at' => now()])
            ]);

            // If AJAX request, return JSON
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Order marked as gathered and moved to Sales Invoice Preparation'
                ]);
            }

            // Otherwise redirect
            return redirect()->back()->with('success', 'Order #' . $order->so_number . ' marked as gathered and moved to Sales Invoice.');

        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function deliveryScheduling()
    {
        $orders = \App\Models\SalesOrder::with('customer', 'preparedBy')
            ->where('status', 'ready_for_delivery')
            ->whereNotIn('type', ['calculator_pos', 'ecom_direct'])
            ->orderBy('signed_at', 'desc')
            ->get();

        $drivers = \App\Models\User::where('position', 'Driver')
            ->where('status', true)
            ->get();

        return view('production.logistic.delivery-scheduling', [
            'orders' => $orders,
            'drivers' => $drivers,
            'title' => 'Delivery Scheduling',
            'role' => 'Dispatcher',
            'sidebar' => 'production'
        ]);
    }

    public function markAsDelivered(Request $request, $id)
    {
        $order = \App\Models\SalesOrder::findOrFail($id);
        
        // Check if this is a COD (Cash on Delivery) order - PAID orders can skip this check
        if ($order->type !== 'paid' && $order->transaction_type === 'COD') {
            // Verify that COD collection has been approved by accounting
            $collection = \App\Models\RiderCollection::where('sales_order_id', $order->id)->first();
            
            if (!$collection) {
                return redirect()->back()->with('error', 'Cannot mark delivery as complete: COD collection not found. Please create a rider collection first.');
            }
            
            if ($collection->status !== 'verified') {
                return redirect()->back()->with('error', 'Cannot mark delivery as complete: COD collection must be verified by accounting first. Current status: ' . $collection->status);
            }
        }
        
        $order->update([
            'status' => 'completed',
        ]);

        return redirect()->back()->with('success', 'Order #' . $order->so_number . ' marked as delivered.');
    }

    public function assignDriver(Request $request, $id)
    {
        $request->validate([
            'driver_id' => 'required|exists:users,id',
            'plate_number' => 'required|string|max:255',
            'delivery_date' => 'required|date',
        ]);

        $order = \App\Models\SalesOrder::findOrFail($id);
        $driver = \App\Models\User::findOrFail($request->driver_id);
        
        // Verify that the selected user is a Driver
        if ($driver->position !== 'Driver') {
            return redirect()->back()->with('error', 'Selected user is not a Driver. Only users with Driver position can be assigned.');
        }
        
        $order->update([
            'driver_id' => $request->driver_id,
            'driver' => $driver->first_name . ' ' . $driver->last_name,
            'plate_number' => $request->plate_number,
            'delivery_date' => $request->delivery_date,
        ]);

        // Create RiderCollection if this is a COD (Cash on Delivery) order
        if ($order->transaction_type === 'COD') {
            // Check if RiderCollection already exists for this order
            $existingCollection = \App\Models\RiderCollection::where('sales_order_id', $order->id)->first();
            
            if (!$existingCollection) {
                // Create new rider collection
                \App\Models\RiderCollection::create([
                    'sales_order_id' => $order->id,
                    'rider_id' => $request->driver_id,
                    'amount_to_collect' => $order->total_amount,
                    'transaction_type' => $order->transaction_type,
                    'status' => 'pending',
                ]);
                
                // Update SO collection status
                $order->update([
                    'collection_status' => 'pending_collection',
                ]);
            }
        }

        return redirect()->back()->with('success', 'Driver ' . $driver->first_name . ' ' . $driver->last_name . ' assigned to Order #' . $order->so_number);
    }

    public function printTransmittal($id)
    {
        $order = \App\Models\SalesOrder::with('customer', 'items.book')->findOrFail($id);

        return view('production.logistic.transmittal-slip', [
            'order' => $order,
        ]);
    }

    public function purchaseOrder()
    {
        $suppliers = \App\Models\Supplier::where('status', 'active')->get();
        $products = \App\Models\Product::where('is_active', true)->get();
        return view('production.logistic.purchase-order', [
            'suppliers' => $suppliers,
            'products' => $products,
            'title' => 'Purchase Order',
            'sidebar' => 'production'
        ]);
    }

    public function storePurchaseOrder(Request $request)
    {
        // Basic validation - would be more robust in production
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'po_number' => 'required|unique:purchase_orders,po_number',
            'date' => 'required|date',
            'items' => 'required|array|min:1',
        ]);

        $po = \App\Models\PurchaseOrder::create([
            'po_number' => $request->po_number,
            'supplier_id' => $request->supplier_id,
            'date' => $request->date,
            'terms' => $request->terms,
            'invoice_number' => $request->invoice_number,
            'total_amount' => $request->total_amount,
            'prepared_by' => auth()->id(),
            'status' => 'ordered'
        ]);

        foreach ($request->items as $item) {
            $po->items()->create([
                'product_id' => $item['product_id'] ?? null,
                'description' => $item['description'],
                'isbn' => $item['isbn'] ?? null,
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'total_amount' => $item['total_amount'],
            ]);
        }

        return redirect()->route('production.logistic.purchase-order-list')->with('success', 'Purchase Order #' . $po->po_number . ' created successfully.');
    }

    public function purchaseOrderList()
    {
        $purchaseOrders = \App\Models\PurchaseOrder::with('supplier', 'preparedBy')
            ->latest()
            ->get();

        return view('production.logistic.purchase-order-list', [
            'purchaseOrders' => $purchaseOrders,
            'title' => 'Purchase Orders',
            'sidebar' => 'production'
        ]);
    }

    public function showPurchaseOrder(Request $request, $id)
    {
        $po = \App\Models\PurchaseOrder::with('supplier', 'items.product', 'preparedBy')->findOrFail($id);

        if ($request->ajax()) {
            return view('production.logistic.partials.purchase-order-modal', compact('po'));
        }

        return view('production.logistic.purchase-order-show', [
            'po' => $po,
            'title' => 'Purchase Order Details',
            'sidebar' => 'production'
        ]);
    }

    public function receivingReportList()
    {
        $reports = \App\Models\ReceivingReport::with('purchaseOrder', 'supplier', 'receivedBy')
            ->latest()
            ->get();

        return view('production.logistic.receiving-report-list', [
            'reports' => $reports,
            'title' => 'Receiving Reports',
            'sidebar' => 'production'
        ]);
    }

    public function showReceivingReport(Request $request, $id)
    {
        $rr = \App\Models\ReceivingReport::with(['purchaseOrder', 'supplier', 'receivedBy', 'items.product'])->findOrFail($id);

        if ($request->ajax()) {
            return view('production.logistic.partials.receiving-report-modal', compact('rr'));
        }

        return view('production.logistic.receiving-report-show', [
            'rr' => $rr,
            'title' => 'Receiving Report Details',
            'sidebar' => 'production'
        ]);
    }

    public function createReceivingReport($po_id = null)
    {
        $purchaseOrder = null;
        if ($po_id) {
            $purchaseOrder = \App\Models\PurchaseOrder::with('items.product', 'supplier')->findOrFail($po_id);
        }

        $openPOs = \App\Models\PurchaseOrder::with('supplier')
            ->whereIn('status', ['ordered', 'partially_received'])
            ->get();

        return view('production.logistic.receiving-report-form', [
            'purchaseOrder' => $purchaseOrder,
            'openPOs' => $openPOs,
            'title' => 'Create Receiving Report',
            'sidebar' => 'production'
        ]);
    }

    public function storeReceivingReport(Request $request)
    {
        $request->validate([
            'purchase_order_id' => 'required|exists:purchase_orders,id',
            'rr_number' => 'required|unique:receiving_reports,rr_number',
            'received_date' => 'required|date',
            'items' => 'required|array|min:1',
        ]);

        $po = \App\Models\PurchaseOrder::findOrFail($request->purchase_order_id);

        $rr = \App\Models\ReceivingReport::create([
            'rr_number' => $request->rr_number,
            'purchase_order_id' => $po->id,
            'supplier_id' => $po->supplier_id,
            'received_date' => $request->received_date,
            'received_by' => auth()->id(),
            'status' => 'posted',
            'notes' => $request->notes
        ]);

        foreach ($request->items as $itemId => $data) {
            if ($data['quantity_received'] > 0) {
                $poItem = \App\Models\PurchaseOrderItem::findOrFail($itemId);
                
                $rr->items()->create([
                    'purchase_order_item_id' => $poItem->id,
                    'product_id' => $poItem->product_id,
                    'quantity_received' => $data['quantity_received'],
                    'unit_cost' => $poItem->unit_price,
                    'total_cost' => $data['quantity_received'] * $poItem->unit_price
                ]);

                // Update PO Item received quantity
                $poItem->increment('received_quantity', $data['quantity_received']);

                // Update Inventory
                if ($poItem->product_id) {
                    $product = \App\Models\Product::find($poItem->product_id);
                    if ($product) {
                        $product->increment('stock', $data['quantity_received']);
                        
                        // Record Inventory Transaction
                        \App\Models\InventoryTransaction::create([
                            'product_id' => $product->id,
                            'type' => 'in',
                            'quantity' => $data['quantity_received'],
                            'location' => 'Main Warehouse', // Default
                            'source' => 'Purchase Order',
                            'supplier' => $po->supplier->company_name,
                            'reference_number' => $rr->rr_number,
                            'unit_cost' => $poItem->unit_price,
                            'total_cost' => $data['quantity_received'] * $poItem->unit_price,
                            'notes' => 'Received via RR #' . $rr->rr_number,
                            'status' => 'completed',
                            'transaction_date' => $request->received_date,
                            'user_id' => auth()->id()
                        ]);
                    }
                }
            }
        }

        // Update PO Overall Status
        $allTotal = $po->items->sum('quantity');
        $allReceived = $po->items->sum('received_quantity');

        if ($allReceived >= $allTotal) {
            $po->update(['status' => 'received']);
        } else if ($allReceived > 0) {
            $po->update(['status' => 'partially_received']);
        }

        // --- ACCOUNTING INTEGRATION ---
        $this->accounting->postReceivingReportEntry($rr);

        return redirect()->route('production.logistic.receiving-report-list')->with('success', 'Receiving Report #' . $rr->rr_number . ' posted and inventory updated.');
    }

    public function driverDashboard()
    {
        $assignedDeliveries = \App\Models\SalesOrder::with(['customer', 'items.book', 'riderCollection'])
            ->where('driver_id', auth()->id())
            ->whereIn('status', ['ready_for_delivery', 'in_transit'])
            ->whereNotIn('type', ['calculator_pos', 'ecom_direct'])
            ->latest()
            ->get();

        return view('production.logistic.driver-dashboard', [
            'assignedDeliveries' => $assignedDeliveries,
            'title' => 'Driver Dashboard',
            'role' => 'Driver',
            'sidebar' => 'production'
        ]);
    }

    public function deliveryTracking()
    {
        $deliveries = \App\Models\SalesOrder::with('customer')
            ->whereIn('status', ['ready_for_delivery', 'in_transit', 'completed'])
            ->whereNotIn('type', ['calculator_pos', 'ecom_direct'])
            ->latest()
            ->get();

        return view('production.logistic.delivery-tracking', [
            'deliveries' => $deliveries,
            'title' => 'Delivery Tracking',
            'role' => 'Dispatcher',
            'sidebar' => 'production'
        ]);
    }

    public function deliveryReceiptList()
    {
        $orders = \App\Models\SalesOrder::with('customer', 'preparedBy')
            ->whereIn('status', ['pending_dr_prep', 'pending_dr_approval', 'ready_for_delivery'])
            ->latest()
            ->get();

        return view('production.logistic.delivery-receipt-list', [
            'orders' => $orders,
            'title' => 'Delivery Receipts',
            'sidebar' => 'production'
        ]);
    }

    public function deliveryReceipt($id = null)
    {
        $order = null;
        if ($id) {
            $order = \App\Models\SalesOrder::with('customer', 'items.book', 'preparedBy')->findOrFail($id);
        }

        return view('production.logistic.delivery-receipt', [
            'order' => $order,
            'title' => 'Delivery Receipt',
            'sidebar' => 'production'
        ]);
    }

    public function markAsDRPrepared($id)
    {
        $order = \App\Models\SalesOrder::findOrFail($id);
        $order->update(['status' => 'pending_dr_approval']);

        // Send Notification to Director if status is "pending_dr_approval"
        $director = \App\Models\User::where('position', 'Director')->first();
        if ($director) {
            try {
                $director->notify(new \App\Notifications\DirectorApprovalRequested($order, 'Sales Order'));
            } catch (\Exception $e) {
                \Log::error("Failed to send Sales Order DR approval notification: " . $e->getMessage());
            }
        }

        return redirect()->back()->with('success', 'DR marked as prepared for Order #' . $order->so_number . '. Pending approval.');
    }

    public function approveDR($id)
    {
        // Role Enforcement: Super Admin OR Production Manager or Logistics Supervisor/Head
        $user = auth()->user();
        $userPos = $user->position;
        $isSuperAdmin = $user->isSuperAdmin();
        
        if (!$isSuperAdmin && !str_contains($userPos, 'Manager') && !str_contains($userPos, 'Supervisor') && !str_contains($userPos, 'Head')) {
             return redirect()->back()->with('error', 'Only Super Admins, Production/Logistics Managers, Supervisors, or Heads can approve Delivery Receipts.');
        }

        $order = \App\Models\SalesOrder::findOrFail($id);
        $order->update([
            'status' => 'ready_for_delivery',
            'dr_prepared_at' => now(),
            'dr_prepared_by' => auth()->id()
        ]);

        return redirect()->back()->with('success', 'DR approved for Order #' . $order->so_number . '. Ready for delivery.');
    }

    public function viewDeliveryForm($id)
    {
        $order = \App\Models\SalesOrder::with(['customer', 'items.book', 'preparedBy', 'siPreparedBy', 'drPreparedBy'])->findOrFail($id);
        
        // Logic to determine form type and title
        $documentType = 'DELIVERY RECEIPT';
        if (str_contains($order->type, 'consignment')) {
            $documentType = 'CONSIGNMENT RECEIPT';
        } elseif ($order->si_prepared_at) {
            $documentType = 'SALES INVOICE';
        } elseif ($order->ar_prepared_at) {
            $documentType = 'ACKNOWLEDGEMENT RECEIPT';
        }

        return view('production.logistic.view-delivery-form', [
            'order' => $order,
            'title' => $documentType,
            'documentType' => $documentType,
            'role' => 'Driver',
            'sidebar' => 'production'
        ]);
    }

    /**
     * Save customer selected quantities for evaluation orders
     * API endpoint for recording what customer chose to keep from evaluation
     */
    public function saveEvaluationSelections(Request $request)
    {
        try {
            $orderId = $request->input('order_id');
            $soNumber = $request->input('so_number');
            $selections = $request->input('selections', []);

            // Validate
            if (!$orderId || empty($selections)) {
                return response()->json(['success' => false, 'message' => 'Invalid order or selections']);
            }

            // Get order
            $order = \App\Models\SalesOrder::with('items.book')->findOrFail($orderId);

            // Verify it's an evaluation order
            if ($order->type !== 'evaluation') {
                return response()->json(['success' => false, 'message' => 'This order is not an evaluation order']);
            }

            // Process selections
            $totalSelectedQty = 0;
            foreach ($selections as $selection) {
                $productName = $selection['product'];
                $selectedQty = floatval($selection['quantity']);

                // Find the SalesOrderItem by product name
                $item = $order->items()
                    ->whereHas('book', function($query) use ($productName) {
                        $query->where('name', $productName);
                    })
                    ->first();

                if ($item) {
                    // Update customer_selected_qty
                    $item->update(['customer_selected_qty' => $selectedQty]);
                    $totalSelectedQty += $selectedQty;
                }
            }

            // Record activity log
            \App\Models\ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'Evaluation selections recorded',
                'description' => 'Customer selections recorded for evaluation SO ' . $soNumber . ' - Total items: ' . $totalSelectedQty,
                'reference_type' => 'SalesOrder',
                'reference_id' => $orderId,
                'details' => json_encode($selections)
            ]);

            return response()->json([
                'success' => true, 
                'message' => 'Customer selections saved successfully - Total items selected: ' . $totalSelectedQty
            ]);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error saving evaluation selections: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function savePickedItems(Request $request)
    {
        try {
            $request->validate([
                'order_id' => 'required|exists:sales_orders,id',
                'so_number' => 'required|string',
                'picked_items' => 'required|array|min:1'
            ]);

            $order = \App\Models\SalesOrder::findOrFail($request->order_id);
            
            // Check if a pick list already exists for this SO (that's not completed)
            $pickList = \App\Models\PickList::where('sales_order_id', $order->id)
                ->where('status', '!=', 'completed')
                ->first();
            
            if (!$pickList) {
                // Generate unique pick list number only if creating new
                $pickListNumber = 'PL-' . $request->so_number . '-' . now()->format('YmdHis');
                
                // Create the PickList record
                $pickList = \App\Models\PickList::create([
                    'sales_order_id' => $order->id,
                    'pick_list_number' => $pickListNumber,
                    'status' => 'in_progress',
                    'prepared_by' => auth()->id(),
                    'notes' => $request->input('notes', null),
                ]);
            } else {
                // Update existing pick list
                $pickList->update([
                    'notes' => $request->input('notes', null),
                ]);
                // Delete old items so we can recreate them
                $pickList->pickListItems()->delete();
            }

            // Get the sales order items for matching
            $soItems = $order->items()->get();

            // Create PickListItem records for each picked item
            foreach ($request->picked_items as $pickedItem) {
                // Try to match by item_index first, then by product name
                $matchedItem = null;
                
                // If item_index is provided, use it directly
                if (isset($pickedItem['item_index'])) {
                    $matchedItem = $soItems[$pickedItem['item_index']] ?? null;
                } else {
                    // Fallback: match by product name
                    $matchedItem = $soItems->first(function ($item) use ($pickedItem) {
                        return $item->book->name === $pickedItem['product'];
                    });
                }

                if ($matchedItem) {
                    \App\Models\PickListItem::create([
                        'pick_list_id' => $pickList->id,
                        'sales_order_item_id' => $matchedItem->id,
                        'requested_qty' => $matchedItem->quantity,
                        'picked_qty' => floatval($pickedItem['picked_qty']),
                        'status' => $pickedItem['status'] ?? 'pending',
                        'notes' => $pickedItem['notes'] ?? null,
                    ]);
                }
            }

            // Update order status to picking to indicate it's in the pick process
            $order->update([
                'status' => 'picking',
                'picked_at' => now(),
                'picked_by' => auth()->id()
            ]);

            // Store activity log for audit trail
            \App\Models\ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'Pick list updated with items',
                'description' => 'Pick list items updated for SO ' . $request->so_number,
                'reference_type' => 'PickList',
                'reference_id' => $pickList->id,
                'details' => json_encode([
                    'pick_list_number' => $pickList->pick_list_number,
                    'items_count' => count($request->picked_items),
                    'total_picked' => array_sum(array_column($request->picked_items, 'picked_qty'))
                ])
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pick list saved successfully for SO ' . $request->so_number,
                'pick_list_id' => $pickList->id,
                'pick_list_number' => $pickList->pick_list_number,
            ]);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error saving picked items: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }
}
