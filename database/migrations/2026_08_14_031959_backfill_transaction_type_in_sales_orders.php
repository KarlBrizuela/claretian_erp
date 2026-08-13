<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class BackfillTransactionTypeInSalesOrders extends Migration
{
    /**
     * Run the migrations.
     * Backfill NULL transaction_type values to 'Credit' and change column from ENUM to string
     * so it can accept all values (COD, Credit, Charge, Prepaid, Check, Other, etc.)
     */
    public function up()
    {
        // Change column from ENUM to string to allow all possible values
        DB::statement("ALTER TABLE sales_orders MODIFY COLUMN transaction_type VARCHAR(50) NOT NULL DEFAULT 'Credit'");

        // Backfill NULL or empty transaction_type values to 'Credit'
        DB::table('sales_orders')
            ->whereNull('transaction_type')
            ->orWhere('transaction_type', '')
            ->update(['transaction_type' => 'Credit']);
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        // No-op: cannot safely revert to ENUM without data loss
    }
}
