<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Change status column from ENUM to VARCHAR(255)
        DB::statement("ALTER TABLE mis_material_reqs MODIFY COLUMN status VARCHAR(255) DEFAULT 'to submit'");

        Schema::table('mis_material_reqs', function (Blueprint $table) {
            $table->unsignedBigInteger('approved_by_admin')->nullable()->after('manager_approved_at');
            $table->timestamp('admin_approved_at')->nullable()->after('approved_by_admin');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mis_material_reqs', function (Blueprint $table) {
            $table->dropColumn(['approved_by_admin', 'admin_approved_at']);
        });

        // Convert back to ENUM
        $enum = "'to submit', 'pending approval', 'Pending Final Approval', 'forwarded to accounting', 'received', 'rejected'";
        DB::statement("ALTER TABLE mis_material_reqs MODIFY COLUMN status ENUM($enum) DEFAULT 'to submit'");
    }
};
