<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE mis_service_requests MODIFY COLUMN status ENUM('to submit', 'pending', 'pending approval', 'Pending HR approval', 'Pending Final Approval', 'approved', 'ongoing', 'on_hold', 'rejected', 'completed') NOT NULL DEFAULT 'to submit'");
    }

    public function down(): void
    {
        DB::statement("UPDATE mis_service_requests SET status = 'pending' WHERE status IN ('to submit', 'pending approval', 'Pending HR approval', 'Pending Final Approval')");
        DB::statement("ALTER TABLE mis_service_requests MODIFY COLUMN status ENUM('pending', 'approved', 'rejected', 'completed', 'on_hold', 'ongoing') NOT NULL DEFAULT 'pending'");
    }
};
