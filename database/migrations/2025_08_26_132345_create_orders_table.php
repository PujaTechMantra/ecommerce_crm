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
        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->string('subscription_type')->nullable();
            $table->enum('order_type', ['Rent','Sell']);
            $table->string('order_number');
            $table->unsignedBigInteger('product_id')->nullable();
            $table->unsignedBigInteger('subscription_id')->nullable()->comment('should be rental_prices id');
            $table->double('deposit_amount', 10, 2)->default(0.00);
            $table->double('rental_amount', 10, 2)->default(0.00);
            $table->decimal('total_price', 10, 2);
            $table->decimal('discount_amount', 10, 2)->default(0.00);
            $table->decimal('final_amount', 10, 2)->default(0.00);
            $table->unsignedInteger('quantity');
            $table->enum('payment_mode', ['Online','Offline'])->nullable();
            $table->enum('payment_status', ['pending','completed','failed','cancelled'])->default('pending');
            $table->text('shipping_address')->nullable();
            $table->unsignedInteger('rent_duration')->nullable()->comment('in days');
            $table->dateTime('rent_start_date')->nullable();
            $table->dateTime('rent_end_date')->nullable();
            $table->dateTime('return_date')->nullable();
            $table->enum('rent_status', ['pending','active','inactive','returned','ready to assign','cancelled','suspended','deallocated'])->default('pending');
            $table->enum('cancel_request', ['Yes','No'])->default('No');
            $table->dateTime('cancel_request_at')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            // optional foreign keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('set null');
            $table->foreign('subscription_id')->references('id')->on('rental_prices')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
