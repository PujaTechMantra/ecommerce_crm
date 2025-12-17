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
        Schema::table('products', function (Blueprint $table) {
            $table->string('slug', 255)
                  ->after('title');

            // Add collection_id before category_id
            $table->unsignedBigInteger('collection_id')
                  ->after('long_desc');

            // Add product_type
            // 1 = Direct, 2 = Variation
            $table->tinyInteger('product_type')
                  ->default(1)->after('long_desc')
                  ->comment('1: Direct, 2: Variation');

            // Optional FK (recommended)
            $table->foreign('collection_id')
                  ->references('id')
                  ->on('collections')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['collection_id']);
            $table->dropColumn([
                'slug',
                'collection_id',
                'product_type'
            ]);
        });
    }
};
