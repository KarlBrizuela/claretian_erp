<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use App\Models\Site;
use App\Models\SiteInventory;
use App\Models\StockTransfer;
use App\Models\Book;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SiteController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:sites,name',
            'code' => 'nullable|string|unique:sites,code',
            'location' => 'nullable|string',
            'description' => 'nullable|string'
        ]);

        try {
            $site = Site::create([
                'name' => $request->name,
                'code' => $request->code,
                'location' => $request->location,
                'description' => $request->description,
                'is_active' => true
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Site created successfully',
                'site' => $site
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating site: ' . $e->getMessage()
            ], 422);
        }
    }

    public function addStock(Request $request)
    {
        $request->validate([
            'site_id' => 'required|exists:sites,id',
            'book_id' => 'required|exists:books,id',
            'quantity' => 'required|integer|min:1',
            'reorder_point' => 'nullable|integer|min:0',
            'max_stock' => 'nullable|integer|min:0'
        ]);

        try {
            $siteInventory = SiteInventory::firstOrCreate(
                [
                    'site_id' => $request->site_id,
                    'book_id' => $request->book_id
                ],
                [
                    'quantity' => 0
                ]
            );

            // Increment quantity
            $siteInventory->increment('quantity', $request->quantity);

            // Update reorder point and max stock if provided
            if ($request->reorder_point !== null) {
                $siteInventory->update(['reorder_point' => $request->reorder_point]);
            }
            if ($request->max_stock !== null) {
                $siteInventory->update(['max_stock' => $request->max_stock]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Stock added successfully to ' . $siteInventory->site->name
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error adding stock: ' . $e->getMessage()
            ], 422);
        }
    }

    public function updateSite(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|unique:sites,name,' . $id,
            'code' => 'nullable|string|unique:sites,code,' . $id,
            'location' => 'nullable|string',
            'description' => 'nullable|string'
        ]);

        try {
            $site = Site::findOrFail($id);
            $site->update([
                'name' => $request->name,
                'code' => $request->code,
                'location' => $request->location,
                'description' => $request->description
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Site updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating site: ' . $e->getMessage()
            ], 422);
        }
    }

    public function transfer(Request $request)
    {
        $request->validate([
            'from_site_id' => 'required|exists:sites,id',
            'to_site_id' => 'required|exists:sites,id',
            'book_id' => 'required|exists:books,id',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string'
        ]);

        // Ensure from and to are different
        if ($request->from_site_id === $request->to_site_id) {
            return response()->json([
                'success' => false,
                'message' => 'Source and destination sites must be different'
            ], 422);
        }

        try {
            // Check if source has enough stock
            $sourceInventory = SiteInventory::where('site_id', $request->from_site_id)
                ->where('book_id', $request->book_id)
                ->first();

            if (!$sourceInventory || $sourceInventory->quantity < $request->quantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient stock at source site'
                ], 422);
            }

            // Create transfer request
            $transfer = StockTransfer::create([
                'from_site_id' => $request->from_site_id,
                'to_site_id' => $request->to_site_id,
                'book_id' => $request->book_id,
                'quantity' => $request->quantity,
                'approval_division' => StockTransfer::approvalDivisionForUser(auth()->user()),
                'notes' => $request->notes,
                'created_by' => auth()->id(),
                'status' => 'pending'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Transfer request submitted for ' . $transfer->approval_division . ' approval'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating transfer: ' . $e->getMessage()
            ], 422);
        }
    }

    public function approveTransfer($id)
    {
        try {
            $transfer = StockTransfer::findOrFail($id);

            $user = auth()->user();

            if (!$transfer->canBeApprovedBy($user)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only the assigned ' . ($transfer->approval_division ?? 'division') . ' manager/supervisor can approve this transfer'
                ], 403);
            }

            if ($transfer->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Transfer is not pending'
                ], 422);
            }

            DB::transaction(function () use ($transfer) {
                $transfer->update([
                    'status' => 'accounting_review',
                    'approved_by' => auth()->id(),
                    'approved_at' => now()
                ]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Transfer approved and forwarded to Accounting'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error approving transfer: ' . $e->getMessage()
            ], 422);
        }
    }

    public function approveAccountingTransfer($id)
    {
        try {
            $transfer = StockTransfer::findOrFail($id);
            $user = auth()->user();

            if (!$transfer->canBeReviewedByAccounting($user)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only Accounting/Admin & Finance can review this transfer'
                ], 403);
            }

            if ($transfer->status !== 'accounting_review') {
                return response()->json([
                    'success' => false,
                    'message' => 'Transfer is not waiting for Accounting review'
                ], 422);
            }

            $transfer->update([
                'status' => 'logistics_assignment',
                'accounting_reviewed_by' => $user->id,
                'accounting_reviewed_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Transfer reviewed and forwarded to Logistics'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error reviewing transfer: ' . $e->getMessage()
            ], 422);
        }
    }

    public function assignLogisticsTransfer(Request $request, $id)
    {
        $request->validate([
            'logistics_assigned_to' => 'required|exists:users,id'
        ]);

        try {
            $transfer = StockTransfer::findOrFail($id);
            $user = auth()->user();

            if (!$transfer->canBeAssignedBy($user)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only Logistics supervisor/manager can assign this transfer'
                ], 403);
            }

            if (!in_array($transfer->status, ['logistics_assignment', 'logistics_assigned'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Transfer is not ready for Logistics assignment'
                ], 422);
            }

            $assignee = User::findOrFail($request->logistics_assigned_to);
            if (!str_contains(strtolower($assignee->position ?? ''), 'logistic')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Selected user must be a Logistics staff'
                ], 422);
            }

            $transfer->update([
                'status' => 'logistics_assigned',
                'logistics_assigned_to' => $assignee->id,
                'logistics_assigned_by' => $user->id,
                'logistics_assigned_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Transfer assigned to ' . $assignee->name
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error assigning transfer: ' . $e->getMessage()
            ], 422);
        }
    }

    public function completeLogisticsTransfer($id)
    {
        try {
            $transfer = StockTransfer::findOrFail($id);
            $user = auth()->user();

            if (!$transfer->canBeCompletedBy($user)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only the assigned Logistics staff can complete this transfer'
                ], 403);
            }

            DB::transaction(function () use ($transfer) {
                if (!$transfer->completeStockMovement()) {
                    throw new \Exception('Transfer is not assigned or source stock is insufficient');
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Transfer completed and stock moved'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error completing transfer: ' . $e->getMessage()
            ], 422);
        }
    }

    public function rejectTransfer($id)
    {
        try {
            $transfer = StockTransfer::findOrFail($id);

            $user = auth()->user();

            if (!$transfer->canBeApprovedBy($user)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only the assigned ' . ($transfer->approval_division ?? 'division') . ' manager/supervisor can reject this transfer'
                ], 403);
            }

            if ($transfer->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Transfer is not pending'
                ], 422);
            }

            $transfer->update([
                'status' => 'rejected',
                'rejection_reason' => request('rejection_reason'),
                'approved_by' => auth()->id(),
                'approved_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Transfer rejected'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error rejecting transfer: ' . $e->getMessage()
            ], 422);
        }
    }

    public function getInventory($siteId)
    {
        try {
            $site = Site::findOrFail($siteId);
            
            // Get real-time site-specific inventory only
            $inventory = SiteInventory::where('site_id', $siteId)
                ->with('book')
                ->where('quantity', '>', 0)
                ->get()
                ->map(function($item) {
                    return [
                        'book_id' => $item->book_id,
                        'book' => [
                            'id' => $item->book->id ?? null,
                            'name' => $item->book->name ?? 'Unknown'
                        ],
                        'quantity' => $item->quantity
                    ];
                });

            return response()->json([
                'success' => true,
                'site_id' => $siteId,
                'site_name' => $site->name,
                'inventory' => $inventory->values()->all()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching inventory: ' . $e->getMessage()
            ], 422);
        }
    }

    public function deleteSite($id)
    {
        try {
            $site = Site::findOrFail($id);
            
            // Delete all related site inventory
            SiteInventory::where('site_id', $id)->delete();
            
            // Delete the site
            $site->delete();

            return response()->json([
                'success' => true,
                'message' => 'Site deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting site: ' . $e->getMessage()
            ], 422);
        }
    }
}
