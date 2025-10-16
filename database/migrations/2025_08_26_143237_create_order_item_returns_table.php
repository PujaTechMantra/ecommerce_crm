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
        Schema::create('order_item_returns', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('order_item_id');
            $table->dateTime('return_date')->nullable();
            $table->enum('return_status', ['on_time','late','damaged','good_condition'])->default('on_time');
            $table->text('return_condition')->nullable();
            $table->decimal('refund_amount', 10, 2)->nullable();
            $table->decimal('actual_amount', 10, 2)->default(0.00);
            $table->enum('refund_category', ['deposit_partial_refund','deposit_full_refund','deposit_no_refund'])->default('deposit_no_refund');
            $table->unsignedBigInteger('refund_initiated_by')->nullable();
            $table->text('damaged_part_image')->nullable();
            $table->float('port_charges')->nullable();
            $table->integer('over_due_days')->default(0);
            $table->float('over_due_amnt')->default(0);
            $table->string('early_return_days')->default('0');
            $table->float('early_return_amount')->default(0);
            $table->dateTime('refund_initiated_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->enum('status', ['in_progress','processed','confirmed','rejected'])->default('in_progress');
            $table->string('txnStatus')->nullable();
            $table->text('reason')->nullable();
            $table->string('transaction_id')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            // optional foreign keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('order_item_id')->references('id')->on('order_items')->onDelete('cascade');
            $table->foreign('refund_initiated_by')->references('id')->on('users')->onDelete('set null');
        
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_item_returns');
    }
};
