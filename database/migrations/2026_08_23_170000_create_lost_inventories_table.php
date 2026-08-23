<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('lost_inventories', function (Blueprint $table) {
            $table->id();
            $table->string('product_type'); // 'book', 'index', 'bundle', 'non_book'
            $table->unsignedBigInteger('book_id')->nullable();
            $table->unsignedBigInteger('book_index_id')->nullable();
            $table->unsignedBigInteger('book_bundle_id')->nullable();
            $table->integer('quantity');
            $table->unsignedBigInteger('site_id')->nullable();
            $table->string('team_name')->nullable();
            $table->text('reason')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamp('lost_date')->useCurrent();
            $table->timestamps();

            $table->foreign('book_id')->references('id')->on('books')->onDelete('cascade');
            $table->foreign('book_index_id')->references('id')->on('book_indices')->onDelete('cascade');
            $table->foreign('book_bundle_id')->references('id')->on('book_bundles')->onDelete('cascade');
            $table->foreign('site_id')->references('id')->on('sites')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lost_inventories');
    }
};
