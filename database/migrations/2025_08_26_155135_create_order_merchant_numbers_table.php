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
        Schema::create('order_merchant_numbers', function (Blueprint $table) {
            $table->bigIncrements('id'); // auto-increment primary key
            $table->enum('type', ['new','renew'])->default('new');
            $table->unsignedBigInteger('order_id');
            $table->string('merchantTxnNo', 50);
            $table->text('redirect_url')->nullable();
            $table->string('secureHash')->nullable();
            $table->string('tranCtx', 100)->nullable();
            $table->decimal('amount', 10, 2);
            $table->timestamp('created_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));

            // Foreign key
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_merchant_numbers');
    }
};
