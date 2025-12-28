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
        // First, add the order_item_id column
        Schema::table('reviews', function (Blueprint $table) {
            $table->foreignId('order_item_id')->nullable()->after('product_id')->constrained('order_items')->nullOnDelete();
        });

        // Then drop the old unique constraint (need to check if it exists)
        // Use raw SQL to drop the constraint if it exists
        try {
            DB::statement('ALTER TABLE reviews DROP INDEX reviews_user_id_product_id_unique');
        } catch (\Exception $e) {
            // Index might not exist or have different name, ignore
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropForeign(['order_item_id']);
            $table->dropColumn('order_item_id');
        });

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
