<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockTransfer extends Model
{
    protected $fillable = [
        'from_site_id',
        'to_site_id',
        'book_id',
        'book_index_id',
        'book_bundle_id',
        'quantity',
        'status',
        'approval_division',
        'notes',
        'batch_id',
        'rejection_reason',
        'created_by',
        'approved_by',
        'approved_at',
        'accounting_reviewed_by',
        'accounting_reviewed_at',
        'logistics_assigned_to',
        'logistics_assigned_by',
        'logistics_assigned_at',
        'completed_by',
        'completed_at'
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'accounting_reviewed_at' => 'datetime',
        'logistics_assigned_at' => 'datetime',
        'completed_at' => 'datetime'
    ];

    public function fromSite()
    {
        return $this->belongsTo(Site::class, 'from_site_id');
    }

    public function toSite()
    {
        return $this->belongsTo(Site::class, 'to_site_id');
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function bookIndex()
    {
        return $this->belongsTo(BookIndex::class, 'book_index_id');
    }

    public function bookBundle()
    {
        return $this->belongsTo(BookBundle::class, 'book_bundle_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getItemNameAttribute()
    {
        if ($this->book_id && $this->book) {
            return $this->book->name;
        }
        if ($this->book_index_id && $this->bookIndex) {
            return ($this->bookIndex->book->name ?? 'Unknown Book') . ' ' . $this->bookIndex->index_value;
        }
        if ($this->book_bundle_id && $this->bookBundle) {
            return $this->bookBundle->name;
        }
        return 'Unknown Item';
    }

    public function getItemTypeAttribute()
    {
        if ($this->book_id) {
            return 'Book';
        }
        if ($this->book_index_id) {
            return 'Index';
        }
        if ($this->book_bundle_id) {
            return 'Bundle';
        }
        return 'Unknown';
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function accountingReviewedBy()
    {
        return $this->belongsTo(User::class, 'accounting_reviewed_by');
    }

    public function logisticsAssignedTo()
    {
        return $this->belongsTo(User::class, 'logistics_assigned_to');
    }

    public function logisticsAssignedBy()
    {
        return $this->belongsTo(User::class, 'logistics_assigned_by');
    }

    public function completedBy()
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    public static function approvalDivisionForUser(?User $user): string
    {
        if (!$user) {
            return 'Production';
        }

        $values = collect([$user->division, $user->department, $user->position])
            ->filter()
            ->map(fn ($value) => strtolower($value));

        if ($values->contains(fn ($value) => str_contains($value, 'marketing'))) {
            return 'Marketing';
        }

        if ($values->contains(fn ($value) => str_contains($value, 'production')
            || str_contains($value, 'logistic')
            || str_contains($value, 'warehouse')
            || str_contains($value, 'dto')
            || str_contains($value, 'ford'))) {
            return 'Production';
        }

        foreach ($user->divisions as $division) {
            $value = strtolower($division->division ?? '');

            if (str_contains($value, 'marketing')) {
                return 'Marketing';
            }

            if (str_contains($value, 'production')) {
                return 'Production';
            }
        }

        return 'Production';
    }

    public function canBeApprovedBy(?User $user): bool
    {
        if (!$user) {
            return false;
        }

        if ($user->isSuperAdmin()) {
            return true;
        }

        $position = strtolower($user->position ?? '');
        $isApprover = str_contains($position, 'manager') || str_contains($position, 'supervisor');

        if (!$isApprover) {
            return false;
        }

        return self::approvalDivisionForUser($user) === $this->approval_division;
    }

    public function canBeReviewedByAccounting(?User $user): bool
    {
        if (!$user) {
            return false;
        }

        if ($user->isSuperAdmin()) {
            return true;
        }

        if ($this->status !== 'accounting_review') {
            return false;
        }

        $values = collect([$user->division, $user->department, $user->position])
            ->filter()
            ->map(fn ($value) => strtolower($value));

        return $values->contains(fn ($value) => str_contains($value, 'accounting')
            || str_contains($value, 'finance')
            || str_contains($value, 'admin & finance'));
    }

    public function canBeSeenByProduction(): bool
    {
        return in_array($this->status, ['logistics_assignment', 'logistics_assigned', 'completed'], true);
    }

    public function canBeAssignedBy(?User $user): bool
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

    public function canBeCompletedBy(?User $user): bool
    {
        if (!$user) {
            return false;
        }

        return $user->isSuperAdmin() || (int) $this->logistics_assigned_to === (int) $user->id;
    }

    public function approve()
    {
        if ($this->status !== 'pending') {
            return false;
        }

        $this->update([
            'status' => 'accounting_review',
            'approved_by' => auth()->id(),
            'approved_at' => now()
        ]);

        return true;
    }

    public function completeStockMovement()
    {
        if ($this->status !== 'logistics_assigned') {
            return false;
        }

        // Check if source site has enough stock
        $sourceQuery = SiteInventory::where('site_id', $this->from_site_id);
        if ($this->book_id) {
            $sourceQuery->where('book_id', $this->book_id);
        } elseif ($this->book_index_id) {
            $sourceQuery->where('book_index_id', $this->book_index_id);
        } elseif ($this->book_bundle_id) {
            $sourceQuery->where('book_bundle_id', $this->book_bundle_id);
        }
        
        $sourceInventory = $sourceQuery->lockForUpdate()->first();

        if (!$sourceInventory || $sourceInventory->quantity < $this->quantity) {
            return false;
        }

        // Deduct from source
        $sourceInventory->decrement('quantity', $this->quantity);

        // Add to destination
        $destQuery = SiteInventory::where('site_id', $this->to_site_id);
        if ($this->book_id) {
            $destQuery->where('book_id', $this->book_id);
        } elseif ($this->book_index_id) {
            $destQuery->where('book_index_id', $this->book_index_id);
        } elseif ($this->book_bundle_id) {
            $destQuery->where('book_bundle_id', $this->book_bundle_id);
        }
        
        $destInventory = $destQuery->first();

        if ($destInventory) {
            $destInventory->increment('quantity', $this->quantity);
        } else {
            SiteInventory::create([
                'site_id' => $this->to_site_id,
                'book_id' => $this->book_id,
                'book_index_id' => $this->book_index_id,
                'book_bundle_id' => $this->book_bundle_id,
                'quantity' => $this->quantity
            ]);
        }

        // Mark transfer as completed
        $this->update([
            'status' => 'completed',
            'completed_by' => auth()->id(),
            'completed_at' => now()
        ]);

        return true;
    }
}
