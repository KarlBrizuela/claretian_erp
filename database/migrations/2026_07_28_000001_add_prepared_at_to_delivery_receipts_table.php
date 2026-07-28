<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPreparedAtToDeliveryReceiptsTable extends Migration
{
    public function up()
    {
        Schema::table('delivery_receipts', function (Blueprint $table) {
            if (!Schema::hasColumn('delivery_receipts', 'prepared_at')) {
                $table->timestamp('prepared_at')->nullable()->after('prepared_by');
            }
        });
    }

    public function down()
    {
        Schema::table('delivery_receipts', function (Blueprint $table) {
            $table->dropColumn('prepared_at');
        });
    }
}
