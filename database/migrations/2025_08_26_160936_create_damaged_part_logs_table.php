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
        Schema::create('damaged_part_logs', function (Blueprint $table) {
            $table->increments('id'); // auto-increment primary key
            $table->unsignedBigInteger('order_item_id');
            $table->unsignedBigInteger('bom_part_id');
            $table->float('price');
            $table->unsignedBigInteger('log_by');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            // Foreign keys
            $table->foreign('order_item_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('bom_part_id')->references('id')->on('bom_parts')->onDelete('cascade');
            $table->foreign('log_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('damaged_part_logs');
    }
};
