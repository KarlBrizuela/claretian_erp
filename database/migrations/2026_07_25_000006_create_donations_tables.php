<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        if (!Schema::hasTable('donors')) {
            Schema::create('donors', function (Blueprint $table) {
                $table->id();
                $table->string('donor_code')->unique();
                $table->string('name');
                $table->string('type'); // Individual, Corporate, Foundation, Religious/Parish
                $table->string('email')->nullable();
                $table->string('phone')->nullable();
                $table->string('tax_id')->nullable();
                $table->boolean('is_recurring')->default(false);
                $table->decimal('total_donated_cash', 15, 2)->default(0.00);
                $table->integer('total_donations_count')->default(0);
                $table->string('status')->default('Active'); // Active, Inactive
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('donation_campaigns')) {
            Schema::create('donation_campaigns', function (Blueprint $table) {
                $table->id();
                $table->string('campaign_code')->unique();
                $table->string('title');
                $table->decimal('target_amount', 15, 2)->default(0.00);
                $table->decimal('raised_amount', 15, 2)->default(0.00);
                $table->date('start_date');
                $table->date('end_date')->nullable();
                $table->string('status')->default('Active'); // Active, Completed
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('donations')) {
            Schema::create('donations', function (Blueprint $table) {
                $table->id();
                $table->string('donation_no')->unique();
                $table->foreignId('donor_id')->constrained('donors')->onDelete('cascade');
                $table->foreignId('campaign_id')->nullable()->constrained('donation_campaigns')->onDelete('set null');
                $table->string('donation_type'); // Cash, Books, Equipment
                $table->decimal('amount', 15, 2)->default(0.00); // Cash or FMV
                $table->text('item_description')->nullable(); // Book title/ISBN or Equipment details
                $table->boolean('is_restricted')->default(false);
                $table->string('restricted_fund_purpose')->nullable();
                $table->string('project_supported')->nullable(); // Bible Distribution, Prison Ministry, Mission Schools
                $table->string('receipt_number')->nullable();
                $table->boolean('tax_doc_issued')->default(false);
                $table->string('tax_cert_number')->nullable();
                $table->date('donation_date');
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('donations');
        Schema::dropIfExists('donation_campaigns');
        Schema::dropIfExists('donors');
    }
};
