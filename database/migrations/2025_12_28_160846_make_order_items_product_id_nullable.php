<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            // Drop the old foreign key constraint
            $table->dropForeign(['product_id']);

            // Make product_id nullable
            $table->foreignId('product_id')->nullable()->change();

            // Add new foreign key with SET NULL on delete
            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['product_id']);

            $table->foreignId('product_id')->change();

            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->onDelete('cascade');
        });
    }
};
