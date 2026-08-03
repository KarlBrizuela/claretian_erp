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
        Schema::table('journal_voucher_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('journal_voucher_requests', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('approved_by');
            }
            if (!Schema::hasColumn('journal_voucher_requests', 'rejected_by')) {
                $table->foreignId('rejected_by')->nullable()->after('manager_approved_at')->constrained('users');
            }
            if (!Schema::hasColumn('journal_voucher_requests', 'rejected_at')) {
                $table->timestamp('rejected_at')->nullable()->after('rejected_by');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('journal_voucher_requests', function (Blueprint $table) {
            if (Schema::hasColumn('journal_voucher_requests', 'rejected_by')) {
                $table->dropForeign(['rejected_by']);
                $table->dropColumn('rejected_by');
            }
            if (Schema::hasColumn('journal_voucher_requests', 'rejected_at')) {
                $table->dropColumn('rejected_at');
            }
        });
    }
};
