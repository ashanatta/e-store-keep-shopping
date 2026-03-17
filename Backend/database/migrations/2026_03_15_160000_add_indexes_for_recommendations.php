<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds indexes for user_id, product_id, category_id to optimize
     * "Users like you also liked" recommendation queries.
     */
    public function up(): void
    {
        $this->addIndex('orders', 'user_id', 'recommendations_user_id_idx');
        $this->addIndex('order_items', 'product_id', 'recommendations_product_id_idx');
        $this->addIndex('order_items', ['order_id', 'product_id'], 'recommendations_order_product_idx');
        $this->addIndex('products', 'category_id', 'recommendations_category_id_idx');
    }

    protected function addIndex(string $table, string|array $columns, string $name): void
    {
        $cols = is_array($columns) ? $columns : [$columns];
        $existing = collect(DB::select("SHOW INDEX FROM {$table}"))->pluck('Key_name')->unique();
        if (!$existing->contains($name)) {
            Schema::table($table, fn (Blueprint $t) => count($cols) === 1
                ? $t->index($cols[0], $name)
                : $t->index($cols, $name));
        }
    }

    public function down(): void
    {
        Schema::table('orders', fn (Blueprint $t) => $t->dropIndex('recommendations_user_id_idx'));
        Schema::table('order_items', fn (Blueprint $t) => $t->dropIndex('recommendations_product_id_idx'));
        Schema::table('order_items', fn (Blueprint $t) => $t->dropIndex('recommendations_order_product_idx'));
        Schema::table('products', fn (Blueprint $t) => $t->dropIndex('recommendations_category_id_idx'));
    }
};
