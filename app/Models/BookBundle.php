<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BookBundle extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'sku',
        'description',
        'price',
        'stock',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Get the books included in this bundle.
     */
    public function books()
    {
        return $this->belongsToMany(Book::class, 'book_bundle_items')
                    ->withPivot('quantity')
                    ->withTimestamps();
    }
}
