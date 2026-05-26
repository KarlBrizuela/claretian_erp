<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteInventory extends Model
{
    protected $table = 'site_inventory';

    protected $fillable = [
        'site_id',
        'book_id',
        'quantity',
        'reorder_point',
        'max_stock'
    ];

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function getStockStatus()
    {
        if ($this->quantity == 0) {
            return 'out_of_stock';
        } elseif ($this->reorder_point && $this->quantity <= $this->reorder_point) {
            return 'low_stock';
        } else {
            return 'in_stock';
        }
    }

    public function getInventoryValue()
    {
        return ($this->book->cost ?? 0) * $this->quantity;
    }
}
