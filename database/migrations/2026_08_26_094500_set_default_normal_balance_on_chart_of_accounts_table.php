<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SetDefaultNormalBalanceOnChartOfAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('chart_of_accounts') && Schema::hasColumn('chart_of_accounts', 'normal_balance')) {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE chart_of_accounts MODIFY normal_balance VARCHAR(255) NOT NULL DEFAULT 'Debit'");
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('chart_of_accounts') && Schema::hasColumn('chart_of_accounts', 'normal_balance')) {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE chart_of_accounts MODIFY normal_balance VARCHAR(255) NOT NULL");
        }
    }
}
