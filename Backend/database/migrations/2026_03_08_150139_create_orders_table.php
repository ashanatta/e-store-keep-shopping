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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('order_number')->unique();
            
            // Shipping Details
            $table->string('full_name');
            $table->string('email');
            $table->string('phone_number');
            $table->string('street_address');
            $table->string('city');
            $table->string('state');
            $table->string('zip_code');
            $table->string('country')->default('USA');
            
            // Payment Details
            $table->string('payment_method'); // 'card', 'stripe', 'paypal', 'cod'
            $table->string('payment_status')->default('pending'); // 'pending', 'paid', 'failed'
            
            // Order Status
            $table->string('status')->default('pending'); // 'pending', 'processing', 'shipped', 'delivered', 'cancelled'
            
            // Totals
            $table->decimal('subtotal', 10, 2);
            $table->decimal('shipping', 10, 2)->default(0.00);
            $table->decimal('tax', 10, 2)->default(0.00);
            $table->decimal('total', 10, 2);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
