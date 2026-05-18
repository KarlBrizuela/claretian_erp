<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('journal_voucher_requests', function (Blueprint $table) {
            $table->string('supporting_documents')->nullable()->after('documents');
        });
    }

    public function down()
    {
        Schema::table('journal_voucher_requests', function (Blueprint $table) {
            $table->dropColumn('supporting_documents');
        });
    }
};
