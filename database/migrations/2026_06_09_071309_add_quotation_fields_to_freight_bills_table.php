<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddQuotationFieldsToFreightBillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('freight_bills', function (Blueprint $table) {
            // Add quotation fields
            if (!Schema::hasColumn('freight_bills', 'quote_number')) {
                $table->string('quote_number')->nullable()->unique();
            }
            if (!Schema::hasColumn('freight_bills', 'quote_date')) {
                $table->date('quote_date')->nullable();
            }
            if (!Schema::hasColumn('freight_bills', 'validity_days')) {
                $table->integer('validity_days')->default(2);
            }
            if (!Schema::hasColumn('freight_bills', 'origin_contact')) {
                $table->string('origin_contact')->nullable();
            }
            if (!Schema::hasColumn('freight_bills', 'origin_address')) {
                $table->text('origin_address')->nullable();
            }
            if (!Schema::hasColumn('freight_bills', 'origin_province')) {
                $table->string('origin_province')->nullable();
            }
            if (!Schema::hasColumn('freight_bills', 'destination_contact')) {
                $table->string('destination_contact')->nullable();
            }
            if (!Schema::hasColumn('freight_bills', 'destination_address')) {
                $table->text('destination_address')->nullable();
            }
            if (!Schema::hasColumn('freight_bills', 'destination_province')) {
                $table->string('destination_province')->nullable();
            }
            if (!Schema::hasColumn('freight_bills', 'service_mode')) {
                $table->string('service_mode')->nullable();
            }
            if (!Schema::hasColumn('freight_bills', 'service_carrier')) {
                $table->string('service_carrier')->nullable();
            }
            if (!Schema::hasColumn('freight_bills', 'service_remarks')) {
                $table->text('service_remarks')->nullable();
            }
            if (!Schema::hasColumn('freight_bills', 'estimated_freight')) {
                $table->decimal('estimated_freight', 15, 2)->default(0);
            }
            if (!Schema::hasColumn('freight_bills', 'valuation_percentage')) {
                $table->decimal('valuation_percentage', 5, 2)->default(1);
            }
            if (!Schema::hasColumn('freight_bills', 'handling_percentage')) {
                $table->decimal('handling_percentage', 5, 2)->default(20);
            }
            if (!Schema::hasColumn('freight_bills', 'quotation_data')) {
                $table->longText('quotation_data')->nullable()->comment('JSON: Complete quotation details including shipment, cargo, and rate breakdown');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('freight_bills', function (Blueprint $table) {
            $columnsToRemove = [
                'quote_number',
                'quote_date',
                'validity_days',
                'origin_contact',
                'origin_address',
                'origin_province',
                'destination_contact',
                'destination_address',
                'destination_province',
                'service_mode',
                'service_carrier',
                'service_remarks',
                'estimated_freight',
                'valuation_percentage',
                'handling_percentage',
                'quotation_data',
            ];
            
            foreach ($columnsToRemove as $column) {
                if (Schema::hasColumn('freight_bills', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
}
