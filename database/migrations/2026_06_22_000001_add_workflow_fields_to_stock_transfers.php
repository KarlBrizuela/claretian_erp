<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('stock_transfers')) {
            return;
        }

        Schema::table('stock_transfers', function (Blueprint $table) {
            if (!Schema::hasColumn('stock_transfers', 'accounting_reviewed_by')) {
                $table->foreignId('accounting_reviewed_by')->nullable()->after('approved_at')->constrained('users')->nullOnDelete();
            }

            if (!Schema::hasColumn('stock_transfers', 'accounting_reviewed_at')) {
                $table->timestamp('accounting_reviewed_at')->nullable()->after('accounting_reviewed_by');
            }

            if (!Schema::hasColumn('stock_transfers', 'logistics_assigned_to')) {
                $table->foreignId('logistics_assigned_to')->nullable()->after('accounting_reviewed_at')->constrained('users')->nullOnDelete();
            }

            if (!Schema::hasColumn('stock_transfers', 'logistics_assigned_by')) {
                $table->foreignId('logistics_assigned_by')->nullable()->after('logistics_assigned_to')->constrained('users')->nullOnDelete();
            }

            if (!Schema::hasColumn('stock_transfers', 'logistics_assigned_at')) {
                $table->timestamp('logistics_assigned_at')->nullable()->after('logistics_assigned_by');
            }

            if (!Schema::hasColumn('stock_transfers', 'completed_by')) {
                $table->foreignId('completed_by')->nullable()->after('logistics_assigned_at')->constrained('users')->nullOnDelete();
            }

            if (!Schema::hasColumn('stock_transfers', 'completed_at')) {
                $table->timestamp('completed_at')->nullable()->after('completed_by');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('stock_transfers')) {
            return;
        }

        Schema::table('stock_transfers', function (Blueprint $table) {
            foreach ([
                'accounting_reviewed_by',
                'logistics_assigned_to',
                'logistics_assigned_by',
                'completed_by',
            ] as $column) {
                if (Schema::hasColumn('stock_transfers', $column)) {
                    $table->dropConstrainedForeignId($column);
                }
            }

            foreach ([
                'accounting_reviewed_at',
                'logistics_assigned_at',
                'completed_at',
            ] as $column) {
                if (Schema::hasColumn('stock_transfers', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
