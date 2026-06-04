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
        Schema::table('activity_logs', function (Blueprint $table) {
            // Add reference columns for polymorphic relationships
            $table->string('reference_type')->nullable()->after('ip_address')->comment('Model type (e.g., SalesOrder, PickList)');
            $table->unsignedBigInteger('reference_id')->nullable()->after('reference_type')->comment('Model ID');
            // Add details for JSON data storage
            $table->json('details')->nullable()->after('reference_id')->comment('Additional details as JSON');
            
            // Create index for efficient lookups
            $table->index(['reference_type', 'reference_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropIndex(['reference_type', 'reference_id']);
            $table->dropColumn(['reference_type', 'reference_id', 'details']);
        });
    }
};
