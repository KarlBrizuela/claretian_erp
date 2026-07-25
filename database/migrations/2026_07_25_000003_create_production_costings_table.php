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
        if (!Schema::hasTable('production_costings')) {
            Schema::create('production_costings', function (Blueprint $table) {
                $table->id();
                $table->string('job_number')->unique();
                $table->foreignId('book_id')->nullable()->constrained('books')->onDelete('set null');
                $table->string('job_title');
                $table->integer('quantity_produced')->default(1000);
                $table->integer('pages_count')->default(100);
                
                // 12 Cost Components
                $table->decimal('paper_cost', 15, 2)->default(0.00);
                $table->decimal('ink_cost', 15, 2)->default(0.00);
                $table->decimal('labor_cost', 15, 2)->default(0.00);
                $table->decimal('electricity_cost', 15, 2)->default(0.00);
                $table->decimal('machine_cost', 15, 2)->default(0.00);
                $table->decimal('binding_cost', 15, 2)->default(0.00);
                $table->decimal('uv_cost', 15, 2)->default(0.00);
                $table->decimal('shrink_wrap_cost', 15, 2)->default(0.00);
                $table->decimal('packaging_cost', 15, 2)->default(0.00);
                $table->decimal('freight_cost', 15, 2)->default(0.00);
                $table->decimal('warehouse_cost', 15, 2)->default(0.00);
                $table->decimal('overhead_cost', 15, 2)->default(0.00);
                
                // Automatic Totals & COGS
                $table->decimal('total_cogs', 15, 2)->default(0.00);
                $table->decimal('unit_cogs', 15, 2)->default(0.00);
                $table->string('status')->default('calculated'); // calculated, finalized, archived
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
        Schema::dropIfExists('production_costings');
    }
};
