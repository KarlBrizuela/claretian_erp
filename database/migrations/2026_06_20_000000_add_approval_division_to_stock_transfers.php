<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('stock_transfers') && !Schema::hasColumn('stock_transfers', 'approval_division')) {
            Schema::table('stock_transfers', function (Blueprint $table) {
                $table->string('approval_division')->nullable()->after('status');
            });
        }

        if (Schema::hasTable('stock_transfers') && Schema::hasColumn('stock_transfers', 'approval_division')) {
            DB::table('stock_transfers')
                ->join('users', 'stock_transfers.created_by', '=', 'users.id')
                ->whereNull('stock_transfers.approval_division')
                ->where(function ($query) {
                    $query->where('users.division', 'like', '%Marketing%')
                        ->orWhere('users.position', 'like', '%Marketing%');
                })
                ->update(['stock_transfers.approval_division' => 'Marketing']);

            DB::table('stock_transfers')
                ->join('users', 'stock_transfers.created_by', '=', 'users.id')
                ->whereNull('stock_transfers.approval_division')
                ->where(function ($query) {
                    $query->where('users.division', 'like', '%Production%')
                        ->orWhere('users.position', 'like', '%Production%')
                        ->orWhere('users.position', 'like', '%Logistic%')
                        ->orWhere('users.position', 'like', '%Warehouse%')
                        ->orWhere('users.position', 'like', '%DTO%')
                        ->orWhere('users.position', 'like', '%Ford%');
                })
                ->update(['stock_transfers.approval_division' => 'Production']);
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('stock_transfers') && Schema::hasColumn('stock_transfers', 'approval_division')) {
            Schema::table('stock_transfers', function (Blueprint $table) {
                $table->dropColumn('approval_division');
            });
        }
    }
};
