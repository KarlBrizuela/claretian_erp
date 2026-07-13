<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixPoAndRrItemsForeignKeys extends Migration
{
    public function up()
    {
        Schema::table('purchase_order_items', function (Blueprint $table) {
            try {
                $table->dropForeign('purchase_order_items_product_id_foreign');
            } catch (\Exception $e) {
                // If it fails or is named differently, catch it
            }
            $table->foreign('product_id')
                  ->references('id')
                  ->on('products')
                  ->onDelete('set null');
        });

        Schema::table('receiving_report_items', function (Blueprint $table) {
            try {
                $table->dropForeign('receiving_report_items_product_id_foreign');
            } catch (\Exception $e) {
                // Catch constraint errors
            }
            $table->foreign('product_id')
                  ->references('id')
                  ->on('products')
                  ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('purchase_order_items', function (Blueprint $table) {
            try {
                $table->dropForeign(['product_id']);
            } catch (\Exception $e) {}
            
            $table->foreign('product_id')
                  ->references('id')
                  ->on('products_old')
                  ->onDelete('set null');
        });

        Schema::table('receiving_report_items', function (Blueprint $table) {
            try {
                $table->dropForeign(['product_id']);
            } catch (\Exception $e) {}
            
            $table->foreign('product_id')
                  ->references('id')
                  ->on('products_old')
                  ->onDelete('set null');
        });
    }
}
