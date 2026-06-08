<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesOrder extends Model
{
    protected $fillable = [
        'customer_id',
        'so_number',
        'type',
        'transaction_type', // COD, Credit, Prepaid, Check, Other
        'terms',
        'ref_number',
        'status', // draft, pending_mkt_approval, pending_acct_approval, picking, gathered, pending_si_prep, pending_si_approval, pending_dr_prep, pending_dr_approval, ready_for_delivery, completed, cancelled
        'total_amount',
        'tax_amount',
        'withholding_tax_amount',
        'discount_amount',
        'discount_percentage',
        'is_non_vat',
        'prepared_by',
        'approved_by_mkt',
        'approved_by_acct',
        'approved_by_prod',
        'signed_by_af_manager',
        'si_prepared_by',
        'dr_prepared_by',
        'mkt_approved_at',
        'acct_approved_at',
        'prod_approved_at',
        'signed_at',
        'si_prepared_at',
        'dr_prepared_at',
        'ar_prepared_at',
        'ar_prepared_by',
        'remarks',
        'billing_address',
        'payment_method',
        'payment_reference',
        'cash_received',
        'change_amount',
        'platform',
        'payment_status',
        'collection_status', // pending_collection, collected, handed_over, reconciled
        'tracking_number',
        'shipping_address',
        'attachment',
        'proof_of_payment',
        'order_list_attachment',
        'transaction_subtype',
        'ecom_platform',
        'platform_order_id',
        'pick_list_attachment',
        'shipping_label_attachment',
        'driver',
        'plate_number',
        'driver_id',
        'delivery_date',
        'packing_data',
        'packing_prepared_by',
        'picked_at',
        'picked_by',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }

    public function items()
    {
        return $this->hasMany(SalesOrderItem::class);
    }

    public function preparedBy()
    {
        return $this->belongsTo(User::class, 'prepared_by');
    }

    public function mktApprovedBy()
    {
        return $this->belongsTo(User::class, 'approved_by_mkt');
    }

    public function acctApprovedBy()
    {
        return $this->belongsTo(User::class, 'approved_by_acct');
    }

    public function prodApprovedBy()
    {
        return $this->belongsTo(User::class, 'approved_by_prod');
    }

    public function siPreparedBy()
    {
        return $this->belongsTo(User::class, 'si_prepared_by');
    }

    public function drPreparedBy()
    {
        return $this->belongsTo(User::class, 'dr_prepared_by');
    }

    public function signedBy()
    {
        return $this->belongsTo(User::class, 'signed_by_af_manager');
    }

    public function driverUser()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function riderCollection()
    {
        return $this->hasOne(RiderCollection::class, 'sales_order_id');
    }

    public function pickLists()
    {
        return $this->hasMany(PickList::class, 'sales_order_id');
    }

    /**
     * Get all activity logs related to this sales order
     */
    public function activities()
    {
        return $this->hasMany(ActivityLog::class, 'reference_id')
            ->where('reference_type', 'SalesOrder');
    }

    public function getDueDateAttribute()
    {
        $baseDate = $this->si_prepared_at ?: ($this->created_at ?: now());
        $days = 0;
        
        switch ($this->terms) {
            case 'Net 15': $days = 15; break;
            case 'Net 30': $days = 30; break;
            case 'Net 60': $days = 60; break;
            case 'Due on receipt': $days = 0; break;
            default: $days = 0;
        }
        
        return \Carbon\Carbon::parse($baseDate)->addDays($days);
    }
}
