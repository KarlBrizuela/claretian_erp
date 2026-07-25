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
        if (!Schema::hasColumn('book_indices', 'price')) {
            Schema::table('book_indices', function (Blueprint $table) {
                $table->decimal('price', 15, 2)->default(0.00)->after('stock');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('book_indices', 'price')) {
            Schema::table('book_indices', function (Blueprint $table) {
                $table->dropColumn('price');
            });
        }
    }
};
