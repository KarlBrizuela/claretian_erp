<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMissingColumnsToSalesOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sales_orders', function (Blueprint $table) {
            $table->string('terms')->nullable()->after('status');
            $table->string('ref_number')->nullable()->after('terms');
            $table->text('shipping_address')->nullable()->after('ref_number');
            $table->text('billing_address')->nullable()->after('shipping_address');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sales_orders', function (Blueprint $table) {
            $table->dropColumn(['terms', 'ref_number', 'shipping_address', 'billing_address']);
        });
    }
}
