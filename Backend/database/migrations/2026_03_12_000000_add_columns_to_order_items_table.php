<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('order_items', 'order_id')) {
                $table->foreignId('order_id')->after('id')->constrained()->onDelete('cascade');
            }
            if (!Schema::hasColumn('order_items', 'product_id')) {
                $table->foreignId('product_id')->after('order_id')->constrained()->onDelete('cascade');
            }
            if (!Schema::hasColumn('order_items', 'product_variant_id')) {
                $table->foreignId('product_variant_id')->nullable()->after('product_id')->constrained()->onDelete('set null');
            }
            if (!Schema::hasColumn('order_items', 'product_name')) {
                $table->string('product_name')->after('product_variant_id');
            }
            if (!Schema::hasColumn('order_items', 'variant_info')) {
                $table->string('variant_info')->nullable()->after('product_name');
            }
            if (!Schema::hasColumn('order_items', 'quantity')) {
                $table->unsignedInteger('quantity')->after('variant_info');
            }
            if (!Schema::hasColumn('order_items', 'price')) {
                $table->decimal('price', 10, 2)->after('quantity');
            }
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            if (Schema::hasColumn('order_items', 'order_id')) {
                $table->dropForeign(['order_id']);
                $table->dropForeign(['product_id']);
                $table->dropForeign(['product_variant_id']);
                $table->dropColumn(['order_id', 'product_id', 'product_variant_id', 'product_name', 'variant_info', 'quantity', 'price']);
            }
        });
    }
};
