<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('team_stock_transfers') && !Schema::hasColumn('team_stock_transfers', 'transfer_type')) {
            Schema::table('team_stock_transfers', function (Blueprint $table) {
                $table->string('transfer_type')->default('transfer')->after('transfer_number');
            });
        }

        if (Schema::hasTable('team_stock_transfer_items') && !Schema::hasColumn('team_stock_transfer_items', 'lost_quantity')) {
            Schema::table('team_stock_transfer_items', function (Blueprint $table) {
                $table->integer('lost_quantity')->default(0)->after('quantity');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('team_stock_transfers') && Schema::hasColumn('team_stock_transfers', 'transfer_type')) {
            Schema::table('team_stock_transfers', function (Blueprint $table) {
                $table->dropColumn('transfer_type');
            });
        }

        if (Schema::hasTable('team_stock_transfer_items') && Schema::hasColumn('team_stock_transfer_items', 'lost_quantity')) {
            Schema::table('team_stock_transfer_items', function (Blueprint $table) {
                $table->dropColumn('lost_quantity');
            });
        }
    }
};
