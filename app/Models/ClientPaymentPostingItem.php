<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientPaymentPostingItem extends Model
{
    protected $fillable = [
        'posting_id',
        'customer_id',
        'bank_date',
        'document_no',
        'amount',
        'proof_attachment',
    ];

    public function posting()
    {
        return $this->belongsTo(ClientPaymentPosting::class, 'posting_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
}
