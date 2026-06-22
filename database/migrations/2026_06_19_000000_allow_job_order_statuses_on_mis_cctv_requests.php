<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        DB::statement("ALTER TABLE mis_cctv_requests MODIFY COLUMN status VARCHAR(255) DEFAULT 'to submit'");
    }

    public function down()
    {
        DB::statement("ALTER TABLE mis_cctv_requests MODIFY COLUMN status ENUM('to submit', 'pending approval', 'Pending HR approval', 'Pending Final Approval', 'approved', 'ongoing', 'on_hold', 'rejected', 'completed') DEFAULT 'to submit'");
    }
};
