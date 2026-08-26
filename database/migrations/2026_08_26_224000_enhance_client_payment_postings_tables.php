<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EnhanceClientPaymentPostingsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('client_payment_posting_items', function (Blueprint $table) {
            $table->string('invoice_no')->nullable()->after('customer_id');
            $table->string('receipt_no')->nullable()->after('invoice_no');
            $table->string('reference_no')->nullable()->after('receipt_no');
            $table->string('payment_method')->nullable()->default('cash')->after('reference_no');
            $table->unsignedBigInteger('chart_of_account_id')->nullable()->after('payment_method');
            $table->string('check_number')->nullable()->after('chart_of_account_id');
            $table->date('check_date')->nullable()->after('check_number');
            $table->string('bank_name')->nullable()->after('check_date');
            $table->date('payment_date')->nullable()->after('bank_name');

            $table->foreign('chart_of_account_id')->references('id')->on('chart_of_accounts')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('client_payment_posting_items', function (Blueprint $table) {
            $table->dropForeign(['chart_of_account_id']);
            $table->dropColumn([
                'invoice_no',
                'receipt_no',
                'reference_no',
                'payment_method',
                'chart_of_account_id',
                'check_number',
                'check_date',
                'bank_name',
                'payment_date'
            ]);
        });
    }
}
