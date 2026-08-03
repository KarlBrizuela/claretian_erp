<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCrPreparedAtToSalesOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sales_orders', function (Blueprint $table) {
            $table->unsignedBigInteger('cr_prepared_by')->nullable()->after('ar_prepared_at');
            $table->datetime('cr_prepared_at')->nullable()->after('cr_prepared_by');
            
            $table->foreign('cr_prepared_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sales_orders', function (Blueprint $table) {
            $table->dropForeign(['cr_prepared_by']);
            $table->dropColumn(['cr_prepared_by', 'cr_prepared_at']);
        });
    }
}
