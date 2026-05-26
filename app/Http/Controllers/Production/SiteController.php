<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use App\Models\Site;
use App\Models\SiteInventory;
use App\Models\StockTransfer;
use App\Models\Book;
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
                'notes' => $request->notes,
                'created_by' => auth()->id(),
                'status' => 'pending'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Transfer request submitted for approval'
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

            // Check permissions - allow managers, super admin, or marketing staff with approval permission
            $user = auth()->user();
            $hasPermission = $user->isSuperAdmin() 
                || $user->hasPermission('marketing.approval_queue')
                || str_contains($user->position, 'Manager');
            
            if (!$hasPermission) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to approve stock transfers'
                ], 403);
            }

            if ($transfer->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Transfer is not pending'
                ], 422);
            }

            DB::transaction(function () use ($transfer) {
                // Deduct from source
                $sourceInventory = SiteInventory::where('site_id', $transfer->from_site_id)
                    ->where('book_id', $transfer->book_id)
                    ->lockForUpdate()
                    ->first();

                if (!$sourceInventory || $sourceInventory->quantity < $transfer->quantity) {
                    throw new \Exception('Insufficient stock at source site');
                }

                $sourceInventory->decrement('quantity', $transfer->quantity);

                // Add to destination
                $destInventory = SiteInventory::where('site_id', $transfer->to_site_id)
                    ->where('book_id', $transfer->book_id)
                    ->first();

                if ($destInventory) {
                    $destInventory->increment('quantity', $transfer->quantity);
                } else {
                    SiteInventory::create([
                        'site_id' => $transfer->to_site_id,
                        'book_id' => $transfer->book_id,
                        'quantity' => $transfer->quantity
                    ]);
                }

                // Mark transfer as completed
                $transfer->update([
                    'status' => 'completed',
                    'approved_by' => auth()->id(),
                    'approved_at' => now()
                ]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Transfer approved and completed'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error approving transfer: ' . $e->getMessage()
            ], 422);
        }
    }

    public function rejectTransfer($id)
    {
        try {
            $transfer = StockTransfer::findOrFail($id);

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
}
