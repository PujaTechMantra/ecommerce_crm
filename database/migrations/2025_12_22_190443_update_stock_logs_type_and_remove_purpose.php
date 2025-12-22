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
        Schema::table('stock_logs', function (Blueprint $table) {
            if (Schema::hasColumn('stock_logs', 'purpose')) {
                $table->dropColumn('purpose');
            }
        });

        DB::statement("
            ALTER TABLE stock_logs 
            MODIFY type ENUM('Add', 'Remove') NOT NULL
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("
            ALTER TABLE stock_logs 
            MODIFY type ENUM('Credit', 'Debit') NOT NULL
        ");

        Schema::table('stock_logs', function (Blueprint $table) {
            $table->enum('purpose', ['Rent', 'Sell', 'New'])->after('type');
        });

    }
};
