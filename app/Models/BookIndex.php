<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookIndex extends Model
{
    protected $table = 'book_indices';

    protected $fillable = [
        'book_id',
        'index_value',
        'stock',
    ];

    protected $casts = [
        'stock' => 'integer',
    ];

    /**
     * Get the book associated with this index mapping.
     */
    public function book()
    {
        return $this->belongsTo(Book::class, 'book_id');
    }

    /**
     * Accessor for full display name: book name + index_value.
     */
    public function getDisplayNameAttribute()
    {
        if (!$this->book) {
            return $this->index_value;
        }
        return $this->book->name . ' ' . $this->index_value;
    }
}
