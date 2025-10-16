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
        Schema::table('user_location_logs', function (Blueprint $table) {
            // Drop existing FK if it exists
            $table->dropForeign(['user_id']);
        });

        Schema::table('user_location_logs', function (Blueprint $table) {
            // Ensure column is nullable
            $table->unsignedBigInteger('user_id')->nullable()->change();

            // Add new FK with ON DELETE SET NULL
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('user_location_logs', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->unsignedBigInteger('user_id')->change(); // back to non-null if needed
        });
    }
};
