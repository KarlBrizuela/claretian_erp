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
            if (!Schema::hasColumn('book_indices', 'custom_name')) {
                $table->string('custom_name')->nullable()->after('index_value');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('book_indices', function (Blueprint $table) {
            if (Schema::hasColumn('book_indices', 'custom_name')) {
                $table->dropColumn('custom_name');
            }
        });
    }
};
