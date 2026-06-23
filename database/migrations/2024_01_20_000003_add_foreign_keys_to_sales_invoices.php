<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddForeignKeysToSalesInvoices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Check if foreign keys already exist before adding them
        $constraints = DB::select("SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_NAME='sales_invoices' AND COLUMN_NAME='customer_id'");
        
        if (empty($constraints)) {
            DB::statement('ALTER TABLE sales_invoices ADD CONSTRAINT sales_invoices_customer_id_foreign FOREIGN KEY (customer_id) REFERENCES customers (customer_id) ON DELETE SET NULL');
        }

        $constraints = DB::select("SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_NAME='sales_invoices' AND COLUMN_NAME='created_by'");
        
        if (empty($constraints)) {
            DB::statement('ALTER TABLE sales_invoices ADD CONSTRAINT sales_invoices_created_by_foreign FOREIGN KEY (created_by) REFERENCES users (id) ON DELETE SET NULL');
        }

        $constraints = DB::select("SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_NAME='sales_invoices' AND COLUMN_NAME='approved_by'");
        
        if (empty($constraints)) {
            DB::statement('ALTER TABLE sales_invoices ADD CONSTRAINT sales_invoices_approved_by_foreign FOREIGN KEY (approved_by) REFERENCES users (id) ON DELETE SET NULL');
        }

        $constraints = DB::select("SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_NAME='sales_invoices' AND COLUMN_NAME='posted_by'");
        
        if (empty($constraints)) {
            DB::statement('ALTER TABLE sales_invoices ADD CONSTRAINT sales_invoices_posted_by_foreign FOREIGN KEY (posted_by) REFERENCES users (id) ON DELETE SET NULL');
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE sales_invoices DROP FOREIGN KEY IF EXISTS sales_invoices_customer_id_foreign');
        DB::statement('ALTER TABLE sales_invoices DROP FOREIGN KEY IF EXISTS sales_invoices_created_by_foreign');
        DB::statement('ALTER TABLE sales_invoices DROP FOREIGN KEY IF EXISTS sales_invoices_approved_by_foreign');
        DB::statement('ALTER TABLE sales_invoices DROP FOREIGN KEY IF EXISTS sales_invoices_posted_by_foreign');
    }
}

