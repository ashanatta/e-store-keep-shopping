<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'name',
        'description',
        'image',
        'category_id',
        'sale_start',
        'sale_end',
        'discount_percentage',
    ];

    protected $casts = [
        'sale_start' => 'datetime',
        'sale_end' => 'datetime',
    ];

    protected $appends = ['min_variant_price', 'is_on_sale', 'final_price'];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function wishlistItems(): HasMany
    {
        return $this->hasMany(WishlistItem::class);
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function getMinVariantPriceAttribute(): float
    {
        if ($this->relationLoaded('variants')) {
            return (float) ($this->variants->min('price') ?? 0);
        }

        return (float) ($this->variants()->min('price') ?? 0);
    }

    public function getIsOnSaleAttribute(): bool
    {
        if (! $this->discount_percentage || ! $this->sale_start || ! $this->sale_end) {
            return false;
        }

        $now = now();

        return $now->between($this->sale_start, $this->sale_end);
    }

    public function getFinalPriceAttribute(): float
    {
        $basePrice = $this->min_variant_price;

        if ($basePrice > 0 && $this->is_on_sale) {
            return $basePrice * (1 - ($this->discount_percentage / 100));
        }

        return $basePrice;
    }
}
