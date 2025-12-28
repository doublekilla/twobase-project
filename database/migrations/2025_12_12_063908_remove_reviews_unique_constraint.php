<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Get foreign key name dynamically
        $foreignKeys = DB::select("
            SELECT CONSTRAINT_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_NAME = 'reviews'
            AND COLUMN_NAME = 'user_id'
            AND REFERENCED_TABLE_NAME IS NOT NULL
            AND TABLE_SCHEMA = DATABASE()
        ");

        // Drop foreign keys if they exist
        Schema::table('reviews', function (Blueprint $table) use ($foreignKeys) {
            foreach ($foreignKeys as $fk) {
                try {
                    $table->dropForeign($fk->CONSTRAINT_NAME);
                } catch (\Exception $e) {
                    // Ignore if already dropped
                }
            }
        });

        // Now drop the unique index
        try {
            DB::statement('ALTER TABLE reviews DROP INDEX reviews_user_id_product_id_unique');
        } catch (\Exception $e) {
            // Index might not exist
        }

        // Re-add the foreign key for user_id without unique constraint
        Schema::table('reviews', function (Blueprint $table) {
            // Check if foreign key exists before adding
            try {
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            } catch (\Exception $e) {
                // Already exists
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Re-add unique constraint
        try {
            Schema::table('reviews', function (Blueprint $table) {
                $table->unique(['user_id', 'product_id'], 'reviews_user_id_product_id_unique');
            });
        } catch (\Exception $e) {
            // Ignore if can't add
        }
    }
};
