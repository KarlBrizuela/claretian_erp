<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeliveryReceiptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delivery_receipts', function (Blueprint $table) {
            $table->id();
            $table->string('dr_number')->unique();
            $table->unsignedBigInteger('so_id')->nullable();
            $table->string('so_number')->nullable();
            $table->unsignedBigInteger('si_id')->nullable();
            $table->string('si_number')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('delivery_address')->nullable();
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->date('delivery_date')->nullable();
            $table->string('status')->default('pending'); // pending, completed, in-transit, cancelled
            $table->unsignedBigInteger('prepared_by')->nullable();
            $table->unsignedBigInteger('received_by')->nullable();
            $table->timestamp('prepared_at')->nullable();
            $table->timestamp('received_at')->nullable();
            $table->longText('notes')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('so_id')->references('id')->on('sales_orders')->onDelete('set null');
            $table->foreign('si_id')->references('id')->on('sales_invoices')->onDelete('set null');
            $table->foreign('customer_id')->references('customer_id')->on('customers')->onDelete('set null');
            $table->foreign('prepared_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('received_by')->references('id')->on('users')->onDelete('set null');

            // Indexes
            $table->index('so_id');
            $table->index('si_id');
            $table->index('customer_id');
            $table->index('status');
            $table->index('delivery_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('delivery_receipts');
    }
}
