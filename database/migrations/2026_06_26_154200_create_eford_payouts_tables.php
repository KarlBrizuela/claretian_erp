<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEfordPayoutsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('eford_payouts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('prepared_by');
            $table->string('period')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->decimal('total_freight', 15, 2)->default(0);
            $table->decimal('total_gross_sales', 15, 2)->default(0);
            $table->json('attachments')->nullable(); // Store multiple attachment paths
            $table->timestamps();

            $table->foreign('prepared_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('customer_id')->references('customer_id')->on('customers')->onDelete('set null');
        });

        Schema::create('eford_payout_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('eford_payout_id');
            $table->string('order_no')->nullable();
            $table->date('date')->nullable();
            $table->string('si_no')->nullable();
            $table->string('customer_name')->nullable();
            $table->decimal('amount', 15, 2)->default(0);
            $table->decimal('freight', 15, 2)->default(0);
            $table->decimal('gross_sales', 15, 2)->default(0);
            $table->string('payment_method')->nullable();
            $table->timestamps();

            $table->foreign('eford_payout_id')->references('id')->on('eford_payouts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('eford_payout_items');
        Schema::dropIfExists('eford_payouts');
    }
}
