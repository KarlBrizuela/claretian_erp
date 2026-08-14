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
        if (Schema::hasTable('team_stock_transfers') && !Schema::hasColumn('team_stock_transfers', 'remarks')) {
            Schema::table('team_stock_transfers', function (Blueprint $table) {
                $table->text('remarks')->nullable()->after('notes');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('team_stock_transfers') && Schema::hasColumn('team_stock_transfers', 'remarks')) {
            Schema::table('team_stock_transfers', function (Blueprint $table) {
                $table->dropColumn('remarks');
            });
        }
    }
};
