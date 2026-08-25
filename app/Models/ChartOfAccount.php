<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChartOfAccount extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'type',
        'category',
        'is_active',
        'parent_id',
        'is_postable',
        'normal_balance',
        'display_order',
    ];

    public function journalEntryItems()
    {
        return $this->hasMany(JournalEntryItem::class);
    }
}
