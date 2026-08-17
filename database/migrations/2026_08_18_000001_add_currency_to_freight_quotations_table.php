<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('freight_quotations', function (Blueprint $table) {
            if (!Schema::hasColumn('freight_quotations', 'currency')) {
                $table->string('currency', 10)->default('PHP')->nullable()->after('freight_option');
            }
        });
    }

    public function down(): void
    {
        Schema::table('freight_quotations', function (Blueprint $table) {
            if (Schema::hasColumn('freight_quotations', 'currency')) {
                $table->dropColumn('currency');
            }
        });
    }
};
