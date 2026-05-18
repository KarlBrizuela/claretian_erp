<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesOrderItem extends Model
{
    protected $fillable = [
        'sales_order_id',
        'book_id',
        'quantity',
        'price',
        'subtotal',
        'unit',
        'source_price_at_sale',
    ];

    public function order()
    {
        return $this->belongsTo(SalesOrder::class, 'sales_order_id');
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    /**
     * Alias for book() — used by views that reference $item->product.
     */
    public function product()
    {
        return $this->belongsTo(Book::class, 'book_id');
    }
}
