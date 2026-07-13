<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFordFieldsToPurchaseOrdersTable extends Migration
{
    public function up()
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->string('source')->default('logistics')->after('status'); // 'ford' or 'logistics'
            $table->string('vendor_name')->nullable()->after('source');
            $table->string('contact_persons')->nullable()->after('vendor_name');
            $table->text('vendor_address')->nullable()->after('contact_persons');
            $table->string('payment_schedule')->nullable()->after('vendor_address');
            $table->string('payment_schedule2')->nullable()->after('payment_schedule');
        });
    }

    public function down()
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropColumn([
                'source',
                'vendor_name',
                'contact_persons',
                'vendor_address',
                'payment_schedule',
                'payment_schedule2',
            ]);
        });
    }
}
