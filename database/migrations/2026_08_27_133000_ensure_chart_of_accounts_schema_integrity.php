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
        if (Schema::hasTable('chart_of_accounts')) {
            Schema::table('chart_of_accounts', function (Blueprint $table) {
                if (!Schema::hasColumn('chart_of_accounts', 'is_postable')) {
                    $table->boolean('is_postable')->default(true)->after('is_active');
                }
                if (!Schema::hasColumn('chart_of_accounts', 'normal_balance')) {
                    $table->string('normal_balance')->default('Debit')->after('is_postable');
                }
                if (!Schema::hasColumn('chart_of_accounts', 'parent_code')) {
                    $table->string('parent_code')->nullable()->after('normal_balance');
                }
                if (!Schema::hasColumn('chart_of_accounts', 'display_order')) {
                    $table->integer('display_order')->default(0)->after('parent_code');
                }
                if (!Schema::hasColumn('chart_of_accounts', 'deleted_at')) {
                    $table->softDeletes();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Safe no-op on rollback
    }
};
