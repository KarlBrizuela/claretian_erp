<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCustomerRepresentativeToFreightQuotationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('freight_quotations', function (Blueprint $table) {
            if (!Schema::hasColumn('freight_quotations', 'customer_representative')) {
                $table->string('customer_representative')->nullable()->after('customer_id');
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
            if (Schema::hasColumn('freight_quotations', 'customer_representative')) {
                $table->dropColumn('customer_representative');
            }
        });
    }
}
