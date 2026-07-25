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
        if (!Schema::hasTable('investments')) {
            Schema::create('investments', function (Blueprint $table) {
                $table->id();
                $table->string('portfolio_code')->unique();
                $table->string('name');
                $table->string('type'); // Time Deposits, Stocks, Mutual Funds, Bonds, Money Market
                $table->string('institution'); // Bank / Broker / Fund Manager
                $table->decimal('principal_amount', 15, 2)->default(0.00);
                $table->decimal('current_value', 15, 2)->default(0.00);
                $table->decimal('interest_rate', 5, 2)->default(0.00); // % p.a.
                $table->date('acquisition_date');
                $table->date('maturity_date')->nullable();
                $table->decimal('total_dividends', 15, 2)->default(0.00);
                $table->decimal('total_interest', 15, 2)->default(0.00);
                $table->decimal('total_return', 15, 2)->default(0.00);
                $table->decimal('roi_percentage', 8, 2)->default(0.00);
                $table->string('status')->default('Active'); // Active, Matured, Liquidated
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('investment_transactions')) {
            Schema::create('investment_transactions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('investment_id')->constrained('investments')->onDelete('cascade');
                $table->string('transaction_type'); // Dividend, Interest, Valuation Update, Reinvestment
                $table->date('transaction_date');
                $table->decimal('amount', 15, 2)->default(0.00);
                $table->string('reference_no')->nullable();
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
        Schema::dropIfExists('investment_transactions');
        Schema::dropIfExists('investments');
    }
};
