<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales_order_items', function (Blueprint $table) {
            // Nullable FK so regular (non-bundle) line items are unaffected
            $table->unsignedBigInteger('bundle_id')->nullable()->after('book_id');

            $table->foreign('bundle_id')
                  ->references('id')
                  ->on('book_bundles')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('sales_order_items', function (Blueprint $table) {
            $table->dropForeign(['bundle_id']);
            $table->dropColumn('bundle_id');
        });
    }
};
