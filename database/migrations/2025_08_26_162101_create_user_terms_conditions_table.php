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
        Schema::create('user_terms_conditions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('mobile')->nullable();
            $table->char('request_id', 24);
            $table->char('group_id', 24)->nullable();
            $table->string('email', 191)->nullable();
            $table->string('status', 50)->default('pending');
            $table->string('request_timestamp')->nullable();
            $table->string('response_timestamp')->nullable();
            $table->string('signer_name', 191)->nullable();
            $table->string('signer_city', 191)->nullable();
            $table->string('signer_state', 191)->nullable();
            $table->string('signer_postal_code', 20)->nullable();
            $table->string('signed_at')->nullable();
            $table->text('signed_url')->nullable();
            $table->longText('response_payload')->nullable(); // JSON check handled at app level if needed
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP'))->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_terms_conditions');
    }
};
