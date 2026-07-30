<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use App\Models\Site;
use App\Models\SiteInventory;
use App\Models\StockTransfer;
use App\Models\Book;
use App\Models\BookIndex;
use App\Models\BookBundle;
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
            'book_id' => 'nullable|exists:books,id',
            'book_index_id' => 'nullable|exists:book_indices,id',
            'book_bundle_id' => 'nullable|exists:book_bundles,id',
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

        $hasBook = $request->filled('book_id');
        $hasIndex = $request->filled('book_index_id');
        $hasBundle = $request->filled('book_bundle_id');

        if (($hasBook ? 1 : 0) + ($hasIndex ? 1 : 0) + ($hasBundle ? 1 : 0) !== 1) {
            return response()->json([
                'success' => false,
                'message' => 'Please provide exactly one of: Book, Book Index, or Book Bundle'
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Check if source has enough stock
            $invQuery = SiteInventory::where('site_id', $request->from_site_id);
            if ($hasBook) {
                $invQuery->where('book_id', $request->book_id);
            } elseif ($hasIndex) {
                $invQuery->where('book_index_id', $request->book_index_id);
            } elseif ($hasBundle) {
                $invQuery->where('book_bundle_id', $request->book_bundle_id);
            }
            
            $sourceInventory = $invQuery->lockForUpdate()->first();

            if (!$sourceInventory || $sourceInventory->quantity < $request->quantity) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient stock at source site'
                ], 422);
            }

            $user = auth()->user();
            $isSuperAdmin = $user && $user->isSuperAdmin();

            if ($isSuperAdmin) {
                $transfer = StockTransfer::create([
                    'from_site_id'            => $request->from_site_id,
                    'to_site_id'              => $request->to_site_id,
                    'book_id'                 => $request->book_id,
                    'book_index_id'           => $request->book_index_id,
                    'book_bundle_id'          => $request->book_bundle_id,
                    'quantity'                => $request->quantity,
                    'approval_division'       => StockTransfer::approvalDivisionForUser($user),
                    'notes'                   => $request->notes,
                    'created_by'              => $user->id,
                    'approved_by'             => $user->id,
                    'approved_at'             => now(),
                    'accounting_reviewed_by'  => $user->id,
                    'accounting_reviewed_at'  => now(),
                    'logistics_assigned_by'   => $user->id,
                    'logistics_assigned_to'   => $user->id,
                    'logistics_assigned_at'   => now(),
                    'completed_by'            => $user->id,
                    'completed_at'            => now(),
                    'status'                  => 'completed'
                ]);

                if (!$transfer->executeStockMovement()) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to execute stock movement for direct transfer'
                    ], 422);
                }

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Stock transfer completed immediately (Direct Transfer).'
                ]);
            } else {
                $transfer = StockTransfer::create([
                    'from_site_id' => $request->from_site_id,
                    'to_site_id' => $request->to_site_id,
                    'book_id' => $request->book_id,
                    'book_index_id' => $request->book_index_id,
                    'book_bundle_id' => $request->book_bundle_id,
                    'quantity' => $request->quantity,
                    'approval_division' => StockTransfer::approvalDivisionForUser($user),
                    'notes' => $request->notes,
                    'created_by' => $user->id,
                    'status' => 'pending'
                ]);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Transfer request submitted for ' . $transfer->approval_division . ' approval'
                ]);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error creating transfer: ' . $e->getMessage()
            ], 422);
        }
    }

    public function transferBatch(Request $request)
    {
        $request->validate([
            'from_site_id'  => 'required|exists:sites,id',
            'to_site_id'    => 'required|exists:sites,id',
            'notes'         => 'nullable|string',
            'items'         => 'required|array|min:1',
            'items.*.type'          => 'required|in:book,index,bundle',
            'items.*.item_id'       => 'required|integer|min:1',
            'items.*.quantity'      => 'required|integer|min:1',
        ]);

        if ($request->from_site_id == $request->to_site_id) {
            return response()->json([
                'success' => false,
                'message' => 'Source and destination sites cannot be the same'
            ], 422);
        }

        $batchId = \Illuminate\Support\Str::uuid()->toString();
        $user = auth()->user();
        $isSuperAdmin = $user && $user->isSuperAdmin();
        $approvalDivision = StockTransfer::approvalDivisionForUser($user);
        $errors = [];
        $created = [];

        DB::beginTransaction();
        try {
            foreach ($request->items as $index => $item) {
                $type     = $item['type'];
                $itemId   = $item['item_id'];
                $quantity = $item['quantity'];

                // Build the source inventory query
                $invQuery = SiteInventory::where('site_id', $request->from_site_id);
                if ($type === 'book') {
                    $invQuery->where('book_id', $itemId);
                } elseif ($type === 'index') {
                    $invQuery->where('book_index_id', $itemId);
                } elseif ($type === 'bundle') {
                    $invQuery->where('book_bundle_id', $itemId);
                }

                $sourceInventory = $invQuery->lockForUpdate()->first();

                if (!$sourceInventory || $sourceInventory->quantity < $quantity) {
                    $label = $type === 'book'
                        ? ('Book #' . $itemId)
                        : ($type === 'index' ? ('Index #' . $itemId) : ('Bundle #' . $itemId));
                    $errors[] = "Insufficient stock for {$label} (have " . ($sourceInventory->quantity ?? 0) . ", need {$quantity})";
                    continue;
                }

                if ($isSuperAdmin) {
                    $transfer = StockTransfer::create([
                        'from_site_id'            => $request->from_site_id,
                        'to_site_id'              => $request->to_site_id,
                        'book_id'                 => $type === 'book'   ? $itemId : null,
                        'book_index_id'           => $type === 'index'  ? $itemId : null,
                        'book_bundle_id'          => $type === 'bundle' ? $itemId : null,
                        'quantity'                => $quantity,
                        'approval_division'       => $approvalDivision,
                        'notes'                   => $request->notes,
                        'batch_id'                => $batchId,
                        'created_by'              => $user->id,
                        'approved_by'             => $user->id,
                        'approved_at'             => now(),
                        'accounting_reviewed_by'  => $user->id,
                        'accounting_reviewed_at'  => now(),
                        'logistics_assigned_by'   => $user->id,
                        'logistics_assigned_to'   => $user->id,
                        'logistics_assigned_at'   => now(),
                        'completed_by'            => $user->id,
                        'completed_at'            => now(),
                        'status'                  => 'completed',
                    ]);

                    if (!$transfer->executeStockMovement()) {
                        $label = $type === 'book'
                            ? ('Book #' . $itemId)
                            : ($type === 'index' ? ('Index #' . $itemId) : ('Bundle #' . $itemId));
                        $errors[] = "Failed to move stock for {$label}";
                        continue;
                    }
                } else {
                    $transfer = StockTransfer::create([
                        'from_site_id'      => $request->from_site_id,
                        'to_site_id'        => $request->to_site_id,
                        'book_id'           => $type === 'book'   ? $itemId : null,
                        'book_index_id'     => $type === 'index'  ? $itemId : null,
                        'book_bundle_id'    => $type === 'bundle' ? $itemId : null,
                        'quantity'          => $quantity,
                        'approval_division' => $approvalDivision,
                        'notes'             => $request->notes,
                        'batch_id'          => $batchId,
                        'created_by'        => $user->id,
                        'status'            => 'pending',
                    ]);
                }

                $created[] = $transfer;
            }

            if (!empty($errors) && empty($created)) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => implode('; ', $errors)
                ], 422);
            }

            DB::commit();

            $msg = $isSuperAdmin
                ? (count($created) . ' item(s) stock transfer completed immediately (Direct Transfer).')
                : (count($created) . ' item(s) transfer request submitted for ' . $approvalDivision . ' approval.');

            if (!empty($errors)) {
                $msg .= ' Skipped: ' . implode('; ', $errors);
            }

            return response()->json([
                'success'    => true,
                'message'    => $msg,
                'batch_id'   => $batchId,
                'created'    => count($created),
                'skipped'    => count($errors),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Batch transfer failed: ' . $e->getMessage()
            ], 500);
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

            if (!in_array($transfer->status, ['pending', 'logistics_assignment', 'logistics_assigned'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Transfer is not ready for Logistics assignment'
                ], 422);
            }

            $assignee = User::findOrFail($request->logistics_assigned_to);
            $pos = strtolower($assignee->position ?? '');
            if (!str_contains($pos, 'logistic') && !str_contains($pos, 'rider') && !str_contains($pos, 'driver') && !str_contains($pos, 'delivery')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Selected user must be a Logistics or delivery staff'
                ], 422);
            }

            $updateData = [
                'status' => 'logistics_assigned',
                'logistics_assigned_to' => $assignee->id,
                'logistics_assigned_by' => $user->id,
                'logistics_assigned_at' => now()
            ];

            // If the status is still pending, we implicitly approve it upon assignment
            if ($transfer->status === 'pending') {
                $updateData['approved_by'] = $user->id;
                $updateData['approved_at'] = now();
            }

            $transfer->update($updateData);

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
                    throw new \Exception('Insufficient stock at source site or invalid state');
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
                ->with(['book', 'bookIndex.book', 'bookBundle'])
                ->where('quantity', '>', 0)
                ->get()
                ->map(function($item) {
                    $type = 'book';
                    $itemId = $item->book_id;
                    $name = $item->book->name ?? 'Unknown';

                    if ($item->book_index_id) {
                        $type = 'index';
                        $itemId = $item->book_index_id;
                        $bookName = $item->bookIndex->book->name ?? 'Unknown';
                        $name = $bookName . ' ' . ($item->bookIndex->index_value ?? '');
                    } elseif ($item->book_bundle_id) {
                        $type = 'bundle';
                        $itemId = $item->book_bundle_id;
                        $name = $item->bookBundle->name ?? 'Unknown Bundle';
                    }

                    return [
                        'book_id' => $item->book_id,
                        'book_index_id' => $item->book_index_id,
                        'book_bundle_id' => $item->book_bundle_id,
                        'item_id' => $itemId,
                        'type' => $type,
                        'name' => $name,
                        'book' => [
                            'id' => $item->book_id,
                            'name' => $name
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
