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
    public function pickListManagement()
    {
        $pickingOrders = \App\Models\SalesOrder::with(['customer', 'items.book', 'preparedBy'])
            ->where('status', 'picking')
            ->latest()
            ->get();

        return view('production.logistic.pick-list-management', [
            'pickingOrders' => $pickingOrders,
        ]);
    }

    public function pickListList()
    {
        $orders = \App\Models\SalesOrder::with('customer', 'preparedBy')
            ->where('status', 'picking')
            ->latest()
            ->get();

        \Illuminate\Support\Facades\Log::debug('PickListList orders count: ' . $orders->count());
        if ($orders->count() > 0) {
            \Illuminate\Support\Facades\Log::debug('First order SO: ' . $orders->first()->so_number);
        }

        return view('production.logistic.pick-list-list', [
            'title' => 'Pick Lists',
            'role' => 'Logistics Staff',
            'sidebar' => 'production',
            'orders' => $orders
        ]);
    }

    public function markAsGathered($id)
    {
        $order = \App\Models\SalesOrder::findOrFail($id);
        
        // Determine next status
        $nextStatus = 'pending_si_prep'; // Default for Paid/Charge/Foreign
        
        if (str_contains($order->type, 'consignment') || $order->type === 'complimentary') {
            $nextStatus = 'pending_dr_prep';
        }
        
        $order->update([
            'status' => $nextStatus
        ]);

        return redirect()->back()->with('success', 'Order #' . $order->so_number . ' marked as gathered and routed to the next stage.');
    }

    public function deliveryScheduling()
    {
        $orders = \App\Models\SalesOrder::with('customer', 'preparedBy')
            ->where('status', 'ready_for_delivery')
            ->whereNotIn('type', ['calculator_pos', 'ecom_direct'])
            ->latest()
            ->get();

        $drivers = \App\Models\User::where('division', 'Production Division')
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
        
        $order->update([
            'status' => 'completed',
        ]);

        return redirect()->back()->with('success', 'Order #' . $order->so_number . ' marked as delivered.');
    }

    public function assignDriver(Request $request, $id)
    {
        $request->validate([
            'driver' => 'required|string|max:255',
            'plate_number' => 'required|string|max:255',
        ]);

        $order = \App\Models\SalesOrder::findOrFail($id);
        
        $order->update([
            'driver' => $request->driver,
            'plate_number' => $request->plate_number,
        ]);

        return redirect()->back()->with('success', 'Driver assigned to Order #' . $order->so_number);
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
        $driverName = auth()->user()->name;

        $assignedDeliveries = \App\Models\SalesOrder::with(['customer', 'items.book'])
            ->where('driver', $driverName)
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

    public function deliveryReceipt()
    {
        return view('production.logistic.delivery-receipt');
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
        // Role Enforcement: Production Manager or Logistics Supervisor/Head
        $userPos = auth()->user()->position;
        if (!str_contains($userPos, 'Manager') && !str_contains($userPos, 'Supervisor') && !str_contains($userPos, 'Head')) {
             return redirect()->back()->with('error', 'Only Production/Logistics Managers, Supervisors, or Heads can approve Delivery Receipts.');
        }

        $order = \App\Models\SalesOrder::findOrFail($id);
        $order->update(['status' => 'ready_for_delivery']);

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
}
