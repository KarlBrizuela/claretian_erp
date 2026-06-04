<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTransactionTypeToRiderCollectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rider_collections', function (Blueprint $table) {
            if (!Schema::hasColumn('rider_collections', 'transaction_type')) {
                $table->enum('transaction_type', ['COD', 'Credit', 'Prepaid', 'Check', 'Other', 'Evaluation'])
                    ->default('COD')
                    ->after('status');
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
        Schema::table('rider_collections', function (Blueprint $table) {
            if (Schema::hasColumn('rider_collections', 'transaction_type')) {
                $table->dropColumn('transaction_type');
            }
        });
    }
}
