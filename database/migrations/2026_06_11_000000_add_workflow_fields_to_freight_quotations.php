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
        Schema::table('freight_quotations', function (Blueprint $table) {
            // Add workflow status
            if (!Schema::hasColumn('freight_quotations', 'workflow_status')) {
                $table->enum('workflow_status', ['draft', 'pending_logistics', 'approved', 'linked_to_so'])->default('draft')->after('status');
            }
            
            // Link to sales order
            if (!Schema::hasColumn('freight_quotations', 'sales_order_id')) {
                $table->unsignedBigInteger('sales_order_id')->nullable()->after('workflow_status');
                $table->foreign('sales_order_id')->references('id')->on('sales_orders')->onDelete('set null');
            }
            
            // Logistics response fields
            if (!Schema::hasColumn('freight_quotations', 'boxes_count')) {
                $table->integer('boxes_count')->nullable()->after('sales_order_id');
            }
            
            if (!Schema::hasColumn('freight_quotations', 'logistics_notes')) {
                $table->text('logistics_notes')->nullable()->after('boxes_count');
            }
            
            if (!Schema::hasColumn('freight_quotations', 'responded_by')) {
                $table->unsignedBigInteger('responded_by')->nullable()->after('logistics_notes');
                $table->foreign('responded_by')->references('id')->on('users')->onDelete('set null');
            }
            
            if (!Schema::hasColumn('freight_quotations', 'responded_at')) {
                $table->timestamp('responded_at')->nullable()->after('responded_by');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('freight_quotations', function (Blueprint $table) {
            // Drop foreign keys if they exist
            try {
                $table->dropForeign(['sales_order_id']);
            } catch (\Exception $e) {
                // Foreign key doesn't exist
            }
            
            try {
                $table->dropForeign(['responded_by']);
            } catch (\Exception $e) {
                // Foreign key doesn't exist
            }
            
            // Drop columns
            $columns = ['workflow_status', 'sales_order_id', 'boxes_count', 'logistics_notes', 'responded_by', 'responded_at'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('freight_quotations', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
