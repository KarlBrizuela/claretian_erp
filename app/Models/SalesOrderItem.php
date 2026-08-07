<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesOrderItem extends Model
{
    protected $fillable = [
        'sales_order_id',
        'book_id',
        'bundle_id',
        'book_index_id',
        'quantity',
        'price',
        'discount_value',
        'discount_type',
        'discount_amount',
        'subtotal',
        'unit',
        'area',
        'source_price_at_sale',
        'customer_selected_qty',
        'sent_qty',
        'returned_qty',
        'selected_qty',
    ];

    public function order()
    {
        return $this->belongsTo(SalesOrder::class, 'sales_order_id');
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function bundle()
    {
        return $this->belongsTo(BookBundle::class, 'bundle_id');
    }

    public function bookIndex()
    {
        return $this->belongsTo(BookIndex::class, 'book_index_id');
    }

    /**
     * Alias for book() — used by views that reference $item->product.
     */
    public function product()
    {
        return $this->belongsTo(Book::class, 'book_id');
    }

    /**
     * Get display name for item regardless of whether it's a book, bundle, or index.
     */
    public function getItemNameAttribute()
    {
        if ($this->bundle_id && $this->bundle) {
            return '[Bundle] ' . $this->bundle->name;
        }

        if ($this->book_index_id && $this->bookIndex) {
            $bookName = $this->bookIndex->book ? $this->bookIndex->book->name : '';
            $indexVal = $this->bookIndex->index_value ?? '';
            return trim(($bookName ? $bookName . ' ' : '') . ($indexVal ? 'Index (' . $indexVal . ')' : 'Index'));
        }

        if ($this->book_id && $this->book) {
            return $this->book->name ?? ($this->book->title ?? ($this->book->product_name ?? 'Book'));
        }

        return $this->product_name ?? ($this->description ?? 'Unknown Item');
    }

    /**
     * Get evaluation status for this item
     * Returns: 'full' (all selected), 'partial' (some returned), 'fully_returned' (none selected), 'not_evaluated' (pending)
     */
    public function getEvaluationStatus()
    {
        if (!$this->sent_qty) {
            return 'not_evaluated';
        }

        if ($this->selected_qty === 0) {
            return 'fully_returned';
        }

        if ($this->selected_qty === $this->sent_qty) {
            return 'full';
        }

        return 'partial';
    }

    /**
     * Calculate returned quantity for this item
     */
    public function getReturnedQtyAttribute()
    {
        return ($this->sent_qty ?? 0) - ($this->selected_qty ?? 0);
    }

    /**
     * Check if this is an evaluation item
     */
    public function isEvaluationItem()
    {
        return $this->order && $this->order->type === 'evaluation' && $this->sent_qty > 0;
    }
}
