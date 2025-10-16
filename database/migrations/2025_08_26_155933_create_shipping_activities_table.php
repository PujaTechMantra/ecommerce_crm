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
        Schema::create('shipping_activities', function (Blueprint $table) {
            $table->integer('id', true); // auto-increment primary key
            $table->unsignedBigInteger('order_id');
            $table->enum('status', [
                'Ride Booked',
                'Payment Received',
                'Ride Canceled',
                'Vehicle Assigned',
                'Ride Started',
                'Ride Completed'
            ]);
            $table->unsignedBigInteger('vehicle_id')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->enum('payment_status', ['Pending','Paid','Refunded'])->default('Pending');
            $table->string('description')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            // Foreign keys
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('vehicle_id')->references('id')->on('stocks')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_activities');
    }
};
