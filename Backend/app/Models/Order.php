<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'order_number',
        'full_name',
        'email',
        'phone_number',
        'street_address',
        'city',
        'state',
        'zip_code',
        'country',
        'payment_method',
        'stripe_payment_intent_id',
        'stripe_client_secret',
        'paypal_order_id',
        'payment_status',
        'status',
        'subtotal',
        'shipping',
        'tax',
        'total',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
