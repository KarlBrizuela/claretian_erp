<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTransactionTypeAndFreightModeToFreightQuotationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('freight_quotations', function (Blueprint $table) {
            if (!Schema::hasColumn('freight_quotations', 'transaction_type')) {
                $table->string('transaction_type', 50)->nullable()->default('paid')->after('customer_id');
            }
            if (!Schema::hasColumn('freight_quotations', 'freight_mode')) {
                $table->string('freight_mode', 255)->nullable()->after('service_mode');
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
            if (Schema::hasColumn('freight_quotations', 'freight_mode')) {
                $table->dropColumn('freight_mode');
            }
            if (Schema::hasColumn('freight_quotations', 'transaction_type')) {
                $table->dropColumn('transaction_type');
            }
        });
    }
}
