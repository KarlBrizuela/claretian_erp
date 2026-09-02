<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Repair consignment sales orders that were prematurely set to 'completed' on DR approval before packing
        \App\Models\SalesOrder::where(function($query) {
                $query->whereIn('type', ['area_consignment', 'area_sales_consignment', 'direct_consignment'])
                      ->orWhere('transaction_type', 'consignment');
            })
            ->where('status', 'completed')
            ->whereNotNull('dr_approved_at')
            ->whereNull('packing_data')
            ->update(['status' => 'ready_for_packing']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
