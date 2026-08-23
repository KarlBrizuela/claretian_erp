<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('purchase_returns', function (Blueprint $table) {
            $table->id();
            $table->string('return_no')->unique();
            $table->foreignId('supplier_id')->constrained('suppliers')->onDelete('cascade');
            $table->foreignId('supplier_invoice_id')->nullable()->constrained('supplier_invoices')->onDelete('set null');
            $table->string('supplier_invoice_no')->nullable();
            $table->date('return_date');
            $table->decimal('refund_amount', 15, 2)->default(0);
            $table->boolean('inventory_deducted')->default(true);
            $table->foreignId('journal_entry_id')->nullable()->constrained('journal_entries')->onDelete('set null');
            $table->string('refund_status')->default('receivable'); // receivable, applied_to_payable, refunded
            $table->text('notes')->nullable();
            $table->foreignId('prepared_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('purchase_return_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_return_id')->constrained('purchase_returns')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->integer('returned_qty')->default(0);
            $table->decimal('unit_cost', 15, 2)->default(0);
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_return_items');
        Schema::dropIfExists('purchase_returns');
    }
};
