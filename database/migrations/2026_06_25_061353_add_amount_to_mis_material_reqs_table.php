<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAmountToMisMaterialReqsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mis_material_reqs', function (Blueprint $table) {
            $table->decimal('amount', 15, 2)->nullable()->after('request_details');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mis_material_reqs', function (Blueprint $table) {
            $table->dropColumn('amount');
        });
    }
}
