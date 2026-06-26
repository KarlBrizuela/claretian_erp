<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentRequestsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('requester_id')->constrained('users')->onDelete('cascade');
            $table->date('date');
            $table->string('payment_to');
            $table->string('payment_for')->nullable();
            $table->date('due_date')->nullable();
            $table->string('po_number')->nullable();
            $table->string('item_receipt')->nullable();
            $table->decimal('total_amount', 15, 2)->default(0.00);
            $table->string('attachment_path')->nullable();
            $table->string('status')->default('pending_director_approval'); // pending_director_approval, pending_admin_finance_approval, approved, scheduled, paid, rejected
            
            // Approvals
            $table->foreignId('director_approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('director_approved_at')->nullable();
            
            $table->foreignId('admin_approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('admin_approved_at')->nullable();
            
            $table->foreignId('finance_approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('finance_approved_at')->nullable();

            // Rejections
            $table->foreignId('rejected_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('rejected_at')->nullable();
            $table->text('rejection_reason')->nullable();

            // Payment Processing / Scheduling
            $table->date('scheduled_payment_date')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('payment_reference')->nullable();
            $table->text('remarks')->nullable();

            $table->timestamps();
        });

        Schema::create('payment_request_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_request_id')->constrained('payment_requests')->onDelete('cascade');
            $table->date('item_date')->nullable();
            $table->string('ref_no')->nullable();
            $table->string('particulars');
            $table->decimal('amount', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payment_request_items');
        Schema::dropIfExists('payment_requests');
    }
}
