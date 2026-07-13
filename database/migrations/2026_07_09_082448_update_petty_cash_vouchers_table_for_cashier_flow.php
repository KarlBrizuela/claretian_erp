<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdatePettyCashVouchersTableForCashierFlow extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('petty_cash_vouchers', function (Blueprint $table) {
            $table->string('proof_attachment')->nullable();
        });

        // Alter status column to string to support wider variety of statuses
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE petty_cash_vouchers MODIFY COLUMN status VARCHAR(255) DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('petty_cash_vouchers', function (Blueprint $table) {
            $table->dropColumn('proof_attachment');
        });

        \Illuminate\Support\Facades\DB::statement("ALTER TABLE petty_cash_vouchers MODIFY COLUMN status ENUM('open', 'liquidated') DEFAULT 'open'");
    }
}
