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
       Schema::table('collections', function (Blueprint $table) {
            // Remove the old is_deleted column if exists
            if (Schema::hasColumn('collections', 'is_deleted')) {
                $table->dropColumn('is_deleted');
            }

            // Add deleted_at column for soft deletes
            if (!Schema::hasColumn('collections', 'deleted_at')) {
                $table->softDeletes()->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('collections', function (Blueprint $table) {
            // Remove soft deletes column
            $table->dropSoftDeletes();

            // Add is_deleted column back
            $table->boolean('is_deleted')->default(0);
        });
    }
};
