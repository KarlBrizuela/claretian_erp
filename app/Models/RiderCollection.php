<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiderCollection extends Model
{
    use HasFactory;

    protected $fillable = [
        'sales_order_id',
        'rider_id',
        'amount_to_collect',
        'amount_collected',
        'status', // pending, collected, handed_over, verified
        'collected_at',
        'handed_over_at',
        'verified_at',
        'collection_notes',
        'customer_signature_photo',
        'reference_photo',
        'verified_by',
        'amount_discrepancy',
        'discrepancy_notes',
    ];

    protected $casts = [
        'collected_at' => 'datetime',
        'handed_over_at' => 'datetime',
        'verified_at' => 'datetime',
    ];

    /**
     * Relationship: Rider Collection belongs to Sales Order
     */
    public function salesOrder()
    {
        return $this->belongsTo(SalesOrder::class, 'sales_order_id');
    }

    /**
     * Relationship: Rider Collection belongs to User (Rider)
     */
    public function rider()
    {
        return $this->belongsTo(User::class, 'rider_id');
    }

    /**
     * Relationship: Verified by User (Cashier)
     */
    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Get the customer associated with this collection
     */
    public function customer()
    {
        return $this->hasOneThrough(Customer::class, SalesOrder::class, 'id', 'customer_id', 'sales_order_id', 'customer_id');
    }

    /**
     * Check if collection has discrepancy
     */
    public function hasDiscrepancy()
    {
        if (!$this->amount_collected) return false;
        return $this->amount_collected != $this->amount_to_collect;
    }

    /**
     * Get discrepancy amount (positive = over-collected, negative = under-collected)
     */
    public function getDiscrepancyAmount()
    {
        if (!$this->amount_collected) return 0;
        return $this->amount_collected - $this->amount_to_collect;
    }

    /**
     * Mark as collected by rider
     */
    public function markAsCollected($amountCollected, $notes = null, $photoPath = null)
    {
        $this->update([
            'amount_collected' => $amountCollected,
            'status' => 'collected',
            'collected_at' => now(),
            'collection_notes' => $notes,
            'customer_signature_photo' => $photoPath,
        ]);

        return $this;
    }

    /**
     * Mark as handed over to cashier
     */
    public function markAsHandedOver()
    {
        $this->update([
            'status' => 'handed_over',
            'handed_over_at' => now(),
        ]);

        return $this;
    }

    /**
     * Verify and reconcile collection
     */
    public function verify($verifiedBy, $discrepancyNotes = null)
    {
        $this->update([
            'status' => 'verified',
            'verified_at' => now(),
            'verified_by' => $verifiedBy,
            'discrepancy_notes' => $discrepancyNotes,
        ]);

        // Update associated Sales Order
        $this->salesOrder->update([
            'collection_status' => 'reconciled',
            'payment_status' => 'paid',
        ]);

        return $this;
    }

    /**
     * Get all pending collections for a rider
     */
    public static function pendingForRider($riderId)
    {
        return static::where('rider_id', $riderId)
            ->where('status', 'pending')
            ->with('salesOrder', 'salesOrder.customer')
            ->get();
    }

    /**
     * Get all collections handed over but not yet verified
     */
    public static function awaitingVerification()
    {
        return static::where('status', 'handed_over')
            ->orWhere('status', 'collected')
            ->with('salesOrder', 'salesOrder.customer', 'rider')
            ->get();
    }

    /**
     * Get collection status badge
     */
    public function getStatusBadge()
    {
        $badges = [
            'pending' => '<span class="badge badge-warning">Pending</span>',
            'collected' => '<span class="badge badge-info">Collected</span>',
            'handed_over' => '<span class="badge badge-secondary">Handed Over</span>',
            'verified' => '<span class="badge badge-success">Verified</span>',
        ];

        return $badges[$this->status] ?? '<span class="badge badge-light">Unknown</span>';
    }
}
