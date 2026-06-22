<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('mis_service_requests', 'department')) {
            Schema::table('mis_service_requests', function (Blueprint $table) {
                $table->string('department')->nullable()->after('nature_of_request')->comment('GSD, MIS, HR, DTO, etc.');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('mis_service_requests', 'department')) {
            Schema::table('mis_service_requests', function (Blueprint $table) {
                $table->dropColumn('department');
            });
        }
    }
};
