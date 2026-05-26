<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    protected $fillable = [
        'name',
        'location',
        'code',
        'description',
        'is_active'
    ];

    public function inventory()
    {
        return $this->hasMany(SiteInventory::class);
    }

    public function outgoingTransfers()
    {
        return $this->hasMany(StockTransfer::class, 'from_site_id');
    }

    public function incomingTransfers()
    {
        return $this->hasMany(StockTransfer::class, 'to_site_id');
    }

    public function getTotalInventoryValue()
    {
        return $this->inventory()
            ->join('books', 'site_inventory.book_id', '=', 'books.id')
            ->selectRaw('SUM(site_inventory.quantity * books.cost) as total')
            ->value('total') ?? 0;
    }

    public function getActiveSites()
    {
        return self::where('is_active', true)->orderBy('name')->get();
    }
}
