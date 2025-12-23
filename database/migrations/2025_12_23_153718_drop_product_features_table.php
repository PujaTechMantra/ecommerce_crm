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
        Schema::table('product_features', function (Blueprint $table) {
            $table->dropForeign('product_features_product_id_foreign');
        });

        // Drop table
        Schema::dropIfExists('product_features');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('product_features', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->string('title');
            $table->timestamps();

            $table->index('product_id', 'product_features_product_id_foreign');
        });

        Schema::table('product_features', function (Blueprint $table) {
            $table->foreign('product_id', 'product_features_product_id_foreign')
                ->references('id')
                ->on('products');
        });
    }
};
