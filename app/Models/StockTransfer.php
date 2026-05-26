<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockTransfer extends Model
{
    protected $fillable = [
        'from_site_id',
        'to_site_id',
        'book_id',
        'quantity',
        'status',
        'notes',
        'rejection_reason',
        'created_by',
        'approved_by',
        'approved_at'
    ];

    protected $casts = [
        'approved_at' => 'datetime'
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

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function approve()
    {
        if ($this->status !== 'pending') {
            return false;
        }

        // Check if source site has enough stock
        $sourceInventory = SiteInventory::where('site_id', $this->from_site_id)
            ->where('book_id', $this->book_id)
            ->first();

        if (!$sourceInventory || $sourceInventory->quantity < $this->quantity) {
            return false;
        }

        // Deduct from source
        $sourceInventory->decrement('quantity', $this->quantity);

        // Add to destination
        $destInventory = SiteInventory::where('site_id', $this->to_site_id)
            ->where('book_id', $this->book_id)
            ->first();

        if ($destInventory) {
            $destInventory->increment('quantity', $this->quantity);
        } else {
            SiteInventory::create([
                'site_id' => $this->to_site_id,
                'book_id' => $this->book_id,
                'quantity' => $this->quantity
            ]);
        }

        // Mark transfer as completed
        $this->update([
            'status' => 'completed',
            'approved_by' => auth()->id(),
            'approved_at' => now()
        ]);

        return true;
    }
}
