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
         Schema::create('coupons', function (Blueprint $table) {
            $table->id(); 
            $table->string('name')->nullable();
            $table->string('slug')->nullable();
            $table->string('coupon_code');
            $table->tinyInteger('coupon_type')
                  ->default(2)
                  ->comment('1: percentage, 2: flat');
            $table->double('amount', 8, 2)->default(100.00);
            $table->unsignedBigInteger('max_time_of_use')->default(1);
            $table->unsignedBigInteger('max_time_one_can_use')->default(1);
            $table->unsignedBigInteger('no_of_usage')->default(0);
            $table->timestamp('start_date')
                  ->default(DB::raw('CURRENT_TIMESTAMP'))
                  ->useCurrentOnUpdate();
            $table->timestamp('end_date')->nullable();
            $table->tinyInteger('status')
                  ->default(1)
                  ->comment('1: active, 0: inactive');
            $table->softDeletes(); 
            $table->timestamp('created_at')
                  ->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')
                  ->default(DB::raw('CURRENT_TIMESTAMP'))
                  ->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
