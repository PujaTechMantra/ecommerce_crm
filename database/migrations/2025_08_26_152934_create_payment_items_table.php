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
        Schema::create('payment_items', function (Blueprint $table) {
            $table->integer('id', true); // auto-increment primary key
            $table->string('payment_for')->nullable();
            $table->unsignedBigInteger('payment_id');
            $table->unsignedBigInteger('product_id')->nullable();
            $table->unsignedBigInteger('vehicle_id')->nullable();
            $table->string('duration')->nullable()->comment('in days');
            $table->enum('type', ['deposit','rental'])->nullable();
            $table->decimal('amount', 10, 2)->default(0.00);
            $table->dateTime('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP'));

            // Add foreign keys
            $table->foreign('payment_id')->references('id')->on('payments')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('set null');
            $table->foreign('vehicle_id')->references('id')->on('stocks')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_items');
    }
};
