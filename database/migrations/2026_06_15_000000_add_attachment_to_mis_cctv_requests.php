<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('mis_cctv_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('mis_cctv_requests', 'attachment')) {
                $table->string('attachment')->nullable()->after('viewing');
            }
        });
    }

    public function down()
    {
        Schema::table('mis_cctv_requests', function (Blueprint $table) {
            if (Schema::hasColumn('mis_cctv_requests', 'attachment')) {
                $table->dropColumn('attachment');
            }
        });
    }
};
