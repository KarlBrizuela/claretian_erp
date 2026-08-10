<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('team_stocks')) {
            Schema::create('team_stocks', function (Blueprint $table) {
                $table->id();
                $table->string('team_name'); // Team A, Team B, Team C, etc.
                $table->unsignedBigInteger('book_id')->nullable();
                $table->unsignedBigInteger('book_index_id')->nullable();
                $table->unsignedBigInteger('book_bundle_id')->nullable();
                $table->integer('quantity')->default(0);
                $table->timestamps();

                $table->index(['team_name', 'book_id']);
                $table->index(['team_name', 'book_index_id']);
                $table->index(['team_name', 'book_bundle_id']);
            });
        }

        if (!Schema::hasTable('team_stock_transfers')) {
            Schema::create('team_stock_transfers', function (Blueprint $table) {
                $table->id();
                $table->string('transfer_number')->unique();
                $table->string('team_name');
                $table->unsignedBigInteger('transferred_by');
                $table->text('notes')->nullable();
                $table->string('status')->default('completed');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('team_stock_transfer_items')) {
            Schema::create('team_stock_transfer_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('team_stock_transfer_id');
                $table->unsignedBigInteger('book_id')->nullable();
                $table->unsignedBigInteger('book_index_id')->nullable();
                $table->unsignedBigInteger('book_bundle_id')->nullable();
                $table->integer('quantity');
                $table->timestamps();

                $table->foreign('team_stock_transfer_id')
                      ->references('id')
                      ->on('team_stock_transfers')
                      ->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('team_stock_transfer_items');
        Schema::dropIfExists('team_stock_transfers');
        Schema::dropIfExists('team_stocks');
    }
};
