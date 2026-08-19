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
                if (!Schema::hasColumn('team_stock_transfer_items', 'picked_qty')) {
                    $table->decimal('picked_qty', 10, 2)->default(0)->after('quantity');
                }
                if (!Schema::hasColumn('team_stock_transfer_items', 'status')) {
                    $table->string('status', 50)->default('Pending')->after('picked_qty');
                }
                if (!Schema::hasColumn('team_stock_transfer_items', 'notes')) {
                    $table->text('notes')->nullable()->after('status');
                }
                if (!Schema::hasColumn('team_stock_transfer_items', 'picked_date')) {
                    $table->date('picked_date')->nullable()->after('notes');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('team_stock_transfer_items')) {
            Schema::table('team_stock_transfer_items', function (Blueprint $table) {
                $table->dropColumn(['picked_qty', 'status', 'notes', 'picked_date']);
            });
        }
    }
};
