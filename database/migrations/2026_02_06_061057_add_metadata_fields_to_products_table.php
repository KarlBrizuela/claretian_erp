<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMetadataFieldsToProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('copyright')->nullable();
            $table->string('book_type')->nullable();
            $table->string('weight')->nullable();
            $table->string('cover_type')->nullable();
            $table->string('royalty')->nullable();
            $table->string('article')->nullable();
            $table->string('sub_category')->nullable();
            $table->string('email')->nullable();
            $table->string('contact_number')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'copyright',
                'book_type',
                'weight',
                'cover_type',
                'royalty',
                'article',
                'sub_category',
                'email',
                'contact_number'
            ]);
        });
    }
}
