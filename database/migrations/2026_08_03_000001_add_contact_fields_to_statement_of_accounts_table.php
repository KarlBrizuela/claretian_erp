<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddContactFieldsToStatementOfAccountsTable extends Migration
{
    public function up()
    {
        Schema::table('statement_of_accounts', function (Blueprint $table) {
            $table->string('contact_person')->nullable()->after('customer_id');
            $table->text('billing_address')->nullable()->after('contact_person');
        });
    }

    public function down()
    {
        Schema::table('statement_of_accounts', function (Blueprint $table) {
            $table->dropColumn(['contact_person', 'billing_address']);
        });
    }
}
