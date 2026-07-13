<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAutoDebitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('auto_debits', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->decimal('amount', 12, 2);
            $table->date('debit_date');
            $table->string('item_reason');
            $table->string('source_origin');
            $table->unsignedBigInteger('prepared_by');
            $table->unsignedBigInteger('director_approved_by')->nullable();
            $table->timestamp('director_approved_at')->nullable();
            $table->unsignedBigInteger('finance_approved_by')->nullable();
            $table->timestamp('finance_approved_at')->nullable();
            $table->string('status')->default('pending_director');
            $table->timestamps();

            $table->foreign('prepared_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('director_approved_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('finance_approved_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('auto_debits');
    }
}
