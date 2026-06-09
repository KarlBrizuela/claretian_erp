<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFreightQuotationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('freight_quotations', function (Blueprint $table) {
            $table->id();
            
            // Quote Header
            $table->string('quote_number')->unique();
            $table->date('quote_date');
            $table->integer('validity_days')->default(2);
            
            // Origin (Pick-up)
            $table->string('origin_contact')->nullable();
            $table->text('origin_address')->nullable();
            $table->string('origin_province')->nullable();
            
            // Destination (Delivery)
            $table->string('destination_contact')->nullable();
            $table->text('destination_address')->nullable();
            $table->string('destination_province')->nullable();
            
            // Service Details
            $table->string('service_mode')->nullable(); // Sea Freight, Air Freight, Land Freight, Mixed
            $table->string('service_carrier')->nullable();
            $table->text('service_remarks')->nullable();
            
            // Cargo Description (JSON)
            $table->longText('cargo_items')->nullable()->comment('JSON: Array of cargo items with qty, package_type, dimensions, gross_weight, vol_weight');
            
            // Rate Breakdown
            $table->decimal('estimated_freight', 15, 2)->default(0);
            $table->decimal('valuation_percentage', 5, 2)->default(1);
            $table->decimal('valuation_charge', 15, 2)->default(0);
            $table->decimal('handling_percentage', 5, 2)->default(20);
            $table->decimal('handling_fee', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            
            // Status & Tracking
            $table->string('status')->default('pending'); // pending, approved, quoted, accepted, rejected
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->text('notes')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('freight_quotations');
    }
}
