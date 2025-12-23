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
        Schema::disableForeignKeyConstraints();

        // Drop table directly (no FK name guessing)
        Schema::dropIfExists('stocks');

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
       Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->string('vehicle_number', 50);
            $table->string('vehicle_track_id', 255)->nullable();
            $table->string('imei_number', 250)->nullable();
            $table->string('chassis_number', 250)->nullable();
            $table->string('friendly_name', 250)->nullable();
            $table->tinyInteger('status')->default(1);
            $table->enum('immobilizer_status', ['MOBILIZE', 'IMMOBILIZE'])->default('MOBILIZE');
            $table->string('immobilizer_request_id', 255)->nullable();
            $table->timestamps();

            $table->foreign('product_id')
                  ->references('id')->on('products')
                  ->onDelete('cascade');
        });
    }
};
