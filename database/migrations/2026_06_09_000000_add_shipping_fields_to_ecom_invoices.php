<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddShippingFieldsToEcomInvoices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sales_orders', function (Blueprint $table) {
            // Add new shipping fields for e-com invoices
            $table->date('day_to_ship')->nullable()->after('ecom_platform')->comment('Scheduled shipping date for e-com invoices');
            $table->string('courier')->nullable()->after('day_to_ship')->comment('Courier service: Lex, Spx, Jnt, Flash, Ninja Van');
            
            // Drop removed fields
            $table->dropColumn(['remarks', 'shipping_label_attachment']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sales_orders', function (Blueprint $table) {
            // Restore dropped columns
            $table->text('remarks')->nullable();
            $table->string('shipping_label_attachment')->nullable();
            
            // Drop added columns
            $table->dropColumn(['day_to_ship', 'courier']);
        });
    }
}
