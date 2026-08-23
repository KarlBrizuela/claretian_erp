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
        if (Schema::hasTable('books') && !Schema::hasColumn('books', 'mibf_price')) {
            Schema::table('books', function (Blueprint $table) {
                $table->decimal('mibf_price', 12, 2)->nullable()->after('price');
            });
        }

        if (Schema::hasTable('book_indices') && !Schema::hasColumn('book_indices', 'mibf_price')) {
            Schema::table('book_indices', function (Blueprint $table) {
                $table->decimal('mibf_price', 12, 2)->nullable()->after('price');
            });
        }

        if (Schema::hasTable('book_bundles') && !Schema::hasColumn('book_bundles', 'mibf_price')) {
            Schema::table('book_bundles', function (Blueprint $table) {
                $table->decimal('mibf_price', 12, 2)->nullable()->after('price');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('books') && Schema::hasColumn('books', 'mibf_price')) {
            Schema::table('books', function (Blueprint $table) {
                $table->dropColumn('mibf_price');
            });
        }

        if (Schema::hasTable('book_indices') && Schema::hasColumn('book_indices', 'mibf_price')) {
            Schema::table('book_indices', function (Blueprint $table) {
                $table->dropColumn('mibf_price');
            });
        }

        if (Schema::hasTable('book_bundles') && Schema::hasColumn('book_bundles', 'mibf_price')) {
            Schema::table('book_bundles', function (Blueprint $table) {
                $table->dropColumn('mibf_price');
            });
        }
    }
};
