<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientPaymentPostingsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('client_payment_postings', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('status')->default('pending');
            $table->unsignedBigInteger('prepared_by');
            $table->timestamps();

            $table->foreign('prepared_by')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('client_payment_posting_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('posting_id');
            $table->unsignedBigInteger('customer_id');
            $table->string('bank_date')->nullable();
            $table->string('document_no')->nullable();
            $table->decimal('amount', 12, 2);
            $table->string('proof_attachment')->nullable();
            $table->timestamps();

            $table->foreign('posting_id')->references('id')->on('client_payment_postings')->onDelete('cascade');
            $table->foreign('customer_id')->references('customer_id')->on('customers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('client_payment_posting_items');
        Schema::dropIfExists('client_payment_postings');
    }
}
