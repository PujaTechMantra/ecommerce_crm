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
        Schema::table('coupons', function (Blueprint $table) {

            // Add new field after coupon_type
            $table->double('value')->after('coupon_type');

            // Rename amount to min_amount
            $table->renameColumn('amount', 'min_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('coupons', function (Blueprint $table) {

            $table->dropColumn('value');
            $table->renameColumn('min_amount', 'amount');
        });
    }
};
