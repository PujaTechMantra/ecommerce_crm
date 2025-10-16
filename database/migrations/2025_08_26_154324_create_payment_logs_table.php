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
        Schema::create('payment_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('gateway', 100)->nullable();
            $table->enum('type', ['Initiate sale','refund'])->default('Initiate sale');
            $table->string('transaction_id', 100)->nullable();
            $table->string('merchant_txn_no', 100)->nullable();
            $table->longText('response_payload')->charset('utf8mb4')->collation('utf8mb4_bin');
            $table->string('status', 50)->nullable();
            $table->string('message', 255)->nullable();
            $table->timestamp('created_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
        });

        // Add JSON check constraint for MySQL 8+
        DB::statement('ALTER TABLE `payment_logs` ADD CONSTRAINT chk_response_payload_json CHECK (JSON_VALID(`response_payload`))');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_logs');
    }
};
