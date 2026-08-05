<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class FixCustomersEnumColumnsToVarchar extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE customers MODIFY COLUMN currency_code VARCHAR(20) NULL DEFAULT 'PHP'");
        DB::statement("ALTER TABLE customers MODIFY COLUMN customer_type VARCHAR(100) NULL");
        DB::statement("ALTER TABLE customers MODIFY COLUMN rep VARCHAR(100) NULL");
        DB::statement("ALTER TABLE customers MODIFY COLUMN class VARCHAR(100) NULL");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
