<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForwarderToFreightQuotationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('freight_quotations', function (Blueprint $table) {
            if (!Schema::hasColumn('freight_quotations', 'forwarder')) {
                $table->string('forwarder', 255)->nullable()->after('service_mode');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('freight_quotations', function (Blueprint $table) {
            if (Schema::hasColumn('freight_quotations', 'forwarder')) {
                $table->dropColumn('forwarder');
            }
        });
    }
}
