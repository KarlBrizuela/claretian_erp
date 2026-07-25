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
        if (!Schema::hasTable('production_fixed_assets')) {
            Schema::create('production_fixed_assets', function (Blueprint $table) {
                $table->id();
                $table->string('asset_code')->unique();
                $table->string('name');
                $table->string('category'); // Digital Press, RISO, Vehicles, Computers, Furniture, Buildings, Other
                $table->date('purchase_date');
                $table->string('supplier')->nullable();
                $table->decimal('purchase_price', 15, 2)->default(0.00);
                $table->date('warranty_expiry')->nullable();
                $table->string('serial_number')->nullable();
                $table->integer('useful_life_years')->default(5);
                $table->decimal('salvage_value', 15, 2)->default(0.00);
                $table->decimal('accumulated_depreciation', 15, 2)->default(0.00);
                $table->decimal('total_repair_cost', 15, 2)->default(0.00);
                $table->decimal('current_value', 15, 2)->default(0.00);
                $table->string('status')->default('Operational'); // Operational, Under Maintenance, Retired, Disposed
                $table->string('location')->default('Main Production Facility');
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('asset_maintenance_logs')) {
            Schema::create('asset_maintenance_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('production_fixed_asset_id')->constrained('production_fixed_assets')->onDelete('cascade');
                $table->date('maintenance_date');
                $table->string('title');
                $table->string('technician')->nullable();
                $table->decimal('repair_cost', 15, 2)->default(0.00);
                $table->text('details')->nullable();
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
        Schema::dropIfExists('asset_maintenance_logs');
        Schema::dropIfExists('production_fixed_assets');
    }
};
