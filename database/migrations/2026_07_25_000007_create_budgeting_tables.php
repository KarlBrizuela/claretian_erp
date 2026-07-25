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
        if (!Schema::hasTable('department_budgets')) {
            Schema::create('department_budgets', function (Blueprint $table) {
                $table->id();
                $table->string('budget_code')->unique();
                $table->integer('fiscal_year')->default(2026);
                $table->string('division'); // Production, Sales & Marketing, Admin & Finance, Executive
                $table->string('department');
                $table->decimal('allocated_budget', 15, 2)->default(0.00);
                $table->decimal('actual_spend', 15, 2)->default(0.00);
                $table->decimal('variance', 15, 2)->default(0.00);
                $table->decimal('percentage_used', 5, 2)->default(0.00);
                $table->decimal('forecasted_spend', 15, 2)->default(0.00);
                $table->string('status')->default('Submitted'); // Submitted, Approved, Revision
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('budget_line_items')) {
            Schema::create('budget_line_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('department_budget_id')->constrained('department_budgets')->onDelete('cascade');
                $table->string('account_category'); // Raw Materials, Machinery, Salaries, Utilities, etc.
                $table->decimal('allocated_amount', 15, 2)->default(0.00);
                $table->decimal('actual_amount', 15, 2)->default(0.00);
                $table->decimal('variance_amount', 15, 2)->default(0.00);
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
        Schema::dropIfExists('budget_line_items');
        Schema::dropIfExists('department_budgets');
    }
};
