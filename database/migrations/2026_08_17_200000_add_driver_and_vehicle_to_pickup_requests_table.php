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
        Schema::table('pickup_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('pickup_requests', 'driver_id')) {
                $table->unsignedBigInteger('driver_id')->nullable()->after('requested_date');
                $table->foreign('driver_id')->references('id')->on('users')->onDelete('set null');
            }
            if (!Schema::hasColumn('pickup_requests', 'driver_name')) {
                $table->string('driver_name')->nullable()->after('driver_id');
            }
            if (!Schema::hasColumn('pickup_requests', 'vehicle')) {
                $table->string('vehicle')->nullable()->after('driver_name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pickup_requests', function (Blueprint $table) {
            if (Schema::hasColumn('pickup_requests', 'driver_id')) {
                $table->dropForeign(['driver_id']);
                $table->dropColumn('driver_id');
            }
            if (Schema::hasColumn('pickup_requests', 'driver_name')) {
                $table->dropColumn('driver_name');
            }
            if (Schema::hasColumn('pickup_requests', 'vehicle')) {
                $table->dropColumn('vehicle');
            }
        });
    }
};
