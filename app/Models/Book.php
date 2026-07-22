<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Book extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'sku',
        'barcode',
        'nbs_barcode',
        'author',
        'publisher',
        'size',
        'pages',
        'copyright',
        'book_type',
        'weight',
        'cover_type',
        'royalty',
        'article',
        'sub_category',
        'sub_category_id',
        'email',
        'contact_number',
        'stock',
        'reorder_point',
        'max_stock',
        'unit',
        'cogs_account',
        'purchase_description',
        'image',
        'item_code',
        'cost',
        'price',
        'category',
        'category_id',
        'is_active',
        'sales_description',
        'shelf_number',
        'rack_number',
        'consignment_owner_id',
        'source_price',
        'markup_amount',
        'is_book',
    ];

    public function consignmentOwner()
    {
        return $this->belongsTo(ConsignmentOwner::class);
    }

    public function bookCategory()
    {
        return $this->belongsTo(BookCategory::class, 'category_id');
    }

    public function bookSubCategory()
    {
        return $this->belongsTo(BookCategory::class, 'sub_category_id');
    }

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'cost' => 'decimal:2',
        'stock' => 'integer',
        'reorder_point' => 'integer',
        'max_stock' => 'integer',
        'pages' => 'integer',
        'source_price' => 'decimal:2',
        'markup_amount' => 'decimal:2',
        'is_book' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($book) {
            if (empty($book->item_code)) {
                // Generate Item Code: BK-00001 or NB-00001
                $maxId = static::max('id') ?? 0;
                $nextId = $maxId + 1;
                $prefix = $book->is_book ? 'BK-' : 'NB-';
                $book->item_code = $prefix . str_pad($nextId, 5, '0', STR_PAD_LEFT);
            }
        });
    }

    /**
     * Get the POS listing for this book (if registered).
     */
    public function product(): HasOne
    {
        return $this->hasOne(Product::class);
    }

    /**
     * Get current stocks in different locations.
     */
    public function stocks(): HasMany
    {
        return $this->hasMany(ProductStock::class);
    }

    /**
     * Get history of stock movements.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(InventoryTransaction::class);
    }
}
