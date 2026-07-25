<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        if (!Schema::hasTable('company_bank_accounts')) {
            Schema::create('company_bank_accounts', function (Blueprint $table) {
                $table->id();
                $table->string('account_code')->unique();
                $table->string('bank_name'); // BDO Unibank, BPI, Metrobank, Landbank
                $table->string('account_name');
                $table->string('account_number');
                $table->string('account_type')->default('Checking'); // Checking, Savings, Treasury
                $table->string('currency')->default('PHP');
                $table->decimal('opening_balance', 15, 2)->default(0.00);
                $table->decimal('current_balance', 15, 2)->default(0.00);
                $table->string('status')->default('Active'); // Active, Inactive
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('cash_transactions')) {
            Schema::create('cash_transactions', function (Blueprint $table) {
                $table->id();
                $table->string('transaction_no')->unique();
                $table->foreignId('bank_account_id')->nullable()->constrained('company_bank_accounts')->onDelete('cascade');
                $table->foreignId('to_bank_account_id')->nullable()->constrained('company_bank_accounts')->onDelete('set null');
                $table->string('transaction_type'); // Deposit, Check Issuance, Transfer, Reconciliation, Petty Cash
                $table->string('category'); // Inflow, Outflow, Transfer
                $table->decimal('amount', 15, 2)->default(0.00);
                $table->string('reference_no')->nullable(); // Check #, Deposit Slip #, Transfer Ref #
                $table->string('payee_or_payer')->nullable();
                $table->date('transaction_date');
                $table->string('status')->default('Cleared'); // Pending, Cleared, Reconciled, Cancelled
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_transactions');
        Schema::dropIfExists('company_bank_accounts');
    }
};
