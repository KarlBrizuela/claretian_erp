<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('freight_quotations', function (Blueprint $table) {
            if (!Schema::hasColumn('freight_quotations', 'freight_option')) {
                $table->string('freight_option')->nullable()->after('service_mode')->comment('freight_collect, freight_billing');
            }
        });
    }

    public function down(): void
    {
        Schema::table('freight_quotations', function (Blueprint $table) {
            if (Schema::hasColumn('freight_quotations', 'freight_option')) {
                $table->dropColumn('freight_option');
            }
        });
    }
};
