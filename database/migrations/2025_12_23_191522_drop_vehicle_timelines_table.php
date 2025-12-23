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
        Schema::disableForeignKeyConstraints();

        Schema::dropIfExists('vehicle_timelines');

        Schema::enableForeignKeyConstraints();

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('vehicle_timelines', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('stock_id')->nullable();
            $table->string('field')->nullable();
            $table->longText('value')->nullable();
            $table->string('unit')->nullable();
            $table->dateTime('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();

            $table->index('stock_id', 'stock_forgen_key_stock_id');
            $table->foreign('stock_id', 'stock_forgen_key_stock_id')
                  ->references('id')->on('stocks')
                  ->onDelete('cascade');
        });
    }
};
