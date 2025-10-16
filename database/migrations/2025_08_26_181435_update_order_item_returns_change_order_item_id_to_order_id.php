<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up()
    {
        Schema::table('order_item_returns', function (Blueprint $table) {
            // Drop existing foreign key for order_item_id
            $table->dropForeign(['order_item_id']);

        });

        Schema::table('order_item_returns', function (Blueprint $table) {
            // Recreate foreign key with new relation to orders table
            $table->foreign('order_item_id')
                ->references('id')
                ->on('orders')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('order_item_returns', function (Blueprint $table) {
            // Drop new foreign key
            $table->dropForeign(['order_item_id']);

        });

        Schema::table('order_item_returns', function (Blueprint $table) {
            // Restore old foreign key to order_items
            $table->foreign('order_item_id')
                ->references('id')
                ->on('order_items')
                ->onDelete('cascade');
        });
    }
};
