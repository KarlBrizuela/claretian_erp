<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales_orders', function (Blueprint $table) {
            if (!Schema::hasColumn('sales_orders', 'forwarder')) {
                $table->string('forwarder')->nullable()->after('freight_option');
            }
        });
    }

    public function down(): void
    {
        Schema::table('sales_orders', function (Blueprint $table) {
            if (Schema::hasColumn('sales_orders', 'forwarder')) {
                $table->dropColumn('forwarder');
            }
        });
    }
};
