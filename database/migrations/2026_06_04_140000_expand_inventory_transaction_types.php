<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // MySQL approach: change the enum to include new types
        // For evaluation orders we need: out_evaluation, in_return_evaluation, out_sold_evaluation
        DB::statement("ALTER TABLE inventory_transactions MODIFY COLUMN type ENUM('in', 'out', 'adjustment', 'out_evaluation', 'in_return_evaluation', 'out_sold_evaluation')");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original enum
        DB::statement("ALTER TABLE inventory_transactions MODIFY COLUMN type ENUM('in', 'out', 'adjustment')");
    }
};
