<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('team_stock_transfer_items')) {
            Schema::table('team_stock_transfer_items', function (Blueprint $table) {
                if (!Schema::hasColumn('team_stock_transfer_items', 'packed_qty')) {
                    $table->decimal('packed_qty', 10, 2)->default(0)->after('picked_qty');
                }
                if (!Schema::hasColumn('team_stock_transfer_items', 'packed_date')) {
                    $table->date('packed_date')->nullable()->after('picked_date');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('team_stock_transfer_items')) {
            Schema::table('team_stock_transfer_items', function (Blueprint $table) {
                $table->dropColumn(['packed_qty', 'packed_date']);
            });
        }
    }
};
