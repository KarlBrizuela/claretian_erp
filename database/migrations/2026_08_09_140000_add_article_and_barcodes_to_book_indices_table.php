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
        Schema::table('book_indices', function (Blueprint $table) {
            if (!Schema::hasColumn('book_indices', 'article')) {
                $table->string('article')->nullable()->after('index_value');
            }
            if (!Schema::hasColumn('book_indices', 'barcode')) {
                $table->string('barcode')->nullable()->after('article');
            }
            if (!Schema::hasColumn('book_indices', 'nbs_barcode')) {
                $table->string('nbs_barcode')->nullable()->after('barcode');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('book_indices', function (Blueprint $table) {
            $columnsToDrop = [];
            if (Schema::hasColumn('book_indices', 'article')) {
                $columnsToDrop[] = 'article';
            }
            if (Schema::hasColumn('book_indices', 'barcode')) {
                $columnsToDrop[] = 'barcode';
            }
            if (Schema::hasColumn('book_indices', 'nbs_barcode')) {
                $columnsToDrop[] = 'nbs_barcode';
            }
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
