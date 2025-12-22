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
        Schema::table('stock_ledgers', function (Blueprint $table) {
            $table->unsignedBigInteger('product_item_id')->after('product_id');
            $table->foreign('product_item_id')
                  ->references('id')
                  ->on('product_items')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_ledgers', function (Blueprint $table) {
            $table->dropForeign(['product_item_id']);
            $table->dropColumn('product_item_id');
        });
    }
};
