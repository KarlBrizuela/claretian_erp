<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePickListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pick_lists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_order_id')->constrained('sales_orders')->onDelete('cascade');
            $table->string('pick_list_number')->unique();
            $table->enum('status', ['draft', 'in_progress', 'completed'])->default('draft');
            $table->foreignId('prepared_by')->constrained('users')->onDelete('restrict');
            $table->foreignId('completed_by')->nullable()->constrained('users')->onDelete('restrict');
            $table->timestamp('completed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Index for querying by sales order and status
            $table->index('sales_order_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pick_lists');
    }
}
