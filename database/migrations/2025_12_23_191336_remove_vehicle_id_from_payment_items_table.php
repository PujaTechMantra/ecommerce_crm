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
          Schema::table('payment_items', function (Blueprint $table) {
            // Drop foreign key first
            $table->dropForeign('stock_vehicle_id_foreign');

            // Then drop column
            $table->dropColumn('vehicle_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         Schema::table('payment_items', function (Blueprint $table) {
            // Re-add column
            $table->unsignedBigInteger('vehicle_id')->nullable();

            // Re-add index & foreign key
            $table->index('vehicle_id', 'stock_vehicle_id_foreign');
            $table->foreign('vehicle_id', 'stock_vehicle_id_foreign')
                ->references('id')->on('stocks')
                ->onDelete('cascade');
        });
    }
};
