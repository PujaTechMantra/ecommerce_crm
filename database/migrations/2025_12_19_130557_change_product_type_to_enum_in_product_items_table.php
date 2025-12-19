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
            // Step 1: Change column to VARCHAR temporarily
            DB::statement("
                ALTER TABLE product_items
                MODIFY product_type VARCHAR(20) NOT NULL
            ");

            // Step 2: Convert numeric values to strings
            DB::statement("
                UPDATE product_items
                SET product_type = CASE
                    WHEN product_type = '1' THEN 'single'
                    WHEN product_type = '2' THEN 'variation'
                    ELSE 'single'
                END
            ");

            // Step 3: Convert column to ENUM
            DB::statement("
                ALTER TABLE product_items
                MODIFY product_type ENUM('single', 'variation') NOT NULL
            ");

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         // Step 1: Convert enum back to VARCHAR
        DB::statement("
            ALTER TABLE product_items
            MODIFY product_type VARCHAR(20) NOT NULL
        ");

        // Step 2: Convert strings back to numbers
        DB::statement("
            UPDATE product_items
            SET product_type = CASE
                WHEN product_type = 'single' THEN '1'
                WHEN product_type = 'variation' THEN '2'
                ELSE '1'
            END
        ");

        // Step 3: Convert back to TINYINT
        DB::statement("
            ALTER TABLE product_items
            MODIFY product_type TINYINT(4) NOT NULL COMMENT '1 = Direct, 2 = Variation'
        ");
    }
};
