<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChartOfAccount extends Model
{
    protected $fillable = [
        'code',
        'name',
        'type',
        'category',
        'is_active',
    ];

    public function journalEntryItems()
    {
        return $this->hasMany(JournalEntryItem::class);
    }
}
