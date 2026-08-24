<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddLostTypeToInventoryTransactions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE inventory_transactions MODIFY COLUMN type ENUM('in', 'out', 'adjustment', 'out_evaluation', 'in_return_evaluation', 'out_sold_evaluation', 'LOST')");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE inventory_transactions MODIFY COLUMN type ENUM('in', 'out', 'adjustment', 'out_evaluation', 'in_return_evaluation', 'out_sold_evaluation')");
    }
}
