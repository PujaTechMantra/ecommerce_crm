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
           $table->dropColumn([
                'stock_qty',
                'stock',
                'position',
                'types',
                'is_selling',
                'base_price',
                'display_price',
                'is_rent',
                'is_driving_licence_required',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->integer('stock_qty')->nullable();
            $table->integer('stock')->nullable();
            $table->integer('position')->nullable();
            $table->string('types')->nullable();
            $table->boolean('is_selling')->default(1);
            $table->decimal('base_price', 10, 2)->nullable();
            $table->decimal('display_price', 10, 2)->nullable();
            $table->boolean('is_rent')->default(0);
            $table->boolean('is_driving_licence_required')->default(0);
        });
    }
};
