<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountsPayableTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 1. Add AP fields to suppliers table if they don't exist
        Schema::table('suppliers', function (Blueprint $table) {
            if (!Schema::hasColumn('suppliers', 'category')) {
                $table->string('category')->default('Paper Suppliers')->after('company_name');
            }
            if (!Schema::hasColumn('suppliers', 'tin')) {
                $table->string('tin')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('suppliers', 'address')) {
                $table->text('address')->nullable()->after('tin');
            }
            if (!Schema::hasColumn('suppliers', 'tax_rate')) {
                $table->decimal('tax_rate', 5, 2)->default(1.00)->after('address'); // Default Withholding Tax %
            }
            if (!Schema::hasColumn('suppliers', 'terms')) {
                $table->string('terms')->default('30 Days')->after('tax_rate');
            }
        });

        // 2. Create supplier_invoices table
        if (!Schema::hasTable('supplier_invoices')) {
            Schema::create('supplier_invoices', function (Blueprint $table) {
                $table->id();
                $table->string('invoice_number')->unique();
                $table->foreignId('supplier_id')->constrained('suppliers')->onDelete('cascade');
                $table->foreignId('purchase_order_id')->nullable()->constrained('purchase_orders')->onDelete('set null');
                $table->foreignId('receiving_report_id')->nullable()->constrained('receiving_reports')->onDelete('set null');
                $table->date('invoice_date');
                $table->date('due_date');
                $table->decimal('subtotal', 15, 2)->default(0);
                $table->decimal('tax_amount', 15, 2)->default(0);
                $table->decimal('withholding_tax_rate', 5, 2)->default(0);
                $table->decimal('withholding_tax_amount', 15, 2)->default(0);
                $table->decimal('total_amount', 15, 2)->default(0);
                $table->decimal('amount_paid', 15, 2)->default(0);
                $table->string('status')->default('unpaid'); // unpaid, partially_paid, paid, overdue
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        // 3. Create supplier_payments table
        if (!Schema::hasTable('supplier_payments')) {
            Schema::create('supplier_payments', function (Blueprint $table) {
                $table->id();
                $table->string('payment_number')->unique();
                $table->foreignId('supplier_id')->constrained('suppliers')->onDelete('cascade');
                $table->foreignId('supplier_invoice_id')->nullable()->constrained('supplier_invoices')->onDelete('set null');
                $table->date('payment_date');
                $table->decimal('amount_paid', 15, 2)->default(0);
                $table->decimal('withholding_tax_amount', 15, 2)->default(0);
                $table->string('payment_method')->default('Check'); // Check, Cash, Bank Transfer, E-Wallet
                $table->string('reference_number')->nullable();
                $table->string('status')->default('completed'); // completed, pending, void
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
    public function down()
    {
        Schema::dropIfExists('supplier_payments');
        Schema::dropIfExists('supplier_invoices');
        
        Schema::table('suppliers', function (Blueprint $table) {
            $columnsToDrop = [];
            if (Schema::hasColumn('suppliers', 'category')) $columnsToDrop[] = 'category';
            if (Schema::hasColumn('suppliers', 'tin')) $columnsToDrop[] = 'tin';
            if (Schema::hasColumn('suppliers', 'address')) $columnsToDrop[] = 'address';
            if (Schema::hasColumn('suppliers', 'tax_rate')) $columnsToDrop[] = 'tax_rate';
            if (Schema::hasColumn('suppliers', 'terms')) $columnsToDrop[] = 'terms';
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
}
