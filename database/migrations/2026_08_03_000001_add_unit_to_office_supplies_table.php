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
        if (!Schema::hasColumn('office_supplies', 'unit')) {
            Schema::table('office_supplies', function (Blueprint $table) {
                $table->string('unit')->default('pcs')->nullable()->after('items_stock');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('office_supplies', 'unit')) {
            Schema::table('office_supplies', function (Blueprint $table) {
                $table->dropColumn('unit');
            });
        }
    }
};
