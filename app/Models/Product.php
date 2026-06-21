<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Product extends Model
{
    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'short_description',
        'price',
        'compare_at_price',
        'sku',
        'stock_quantity',
        'weight',
        'is_active',
        'is_featured',
        'meta_title',
        'meta_description',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
    ];

    /**
     * Category relation.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Product images.
     */
    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    /**
     * Primary image relation.
     */
    public function primaryImage(): HasOne
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }

    /**
     * Helper to get primary image URL or fallback.
     */
    public function getPrimaryImageUrlAttribute(): string
    {
        $primary = $this->primaryImage;
        if ($primary) {
            return $primary->image_url;
        }

        $first = $this->images()->first();
        if ($first) {
            return $first->image_url;
        }

        return 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=400&h=400&fit=crop';
    }

    /**
     * Product variants.
     */
    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    /**
     * Scope active products.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope featured products.
     */
    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    /**
     * Get mock rating.
     */
    public function getRatingAttribute(): float
    {
        return 4.0 + (($this->id * 3) % 11) / 10.0;
    }

    /**
     * Get mock rating count.
     */
    public function getRatingCountAttribute(): int
    {
        return (($this->id * 17) % 150) + 10;
    }

    /**
     * Get the wishlist items associated with the product.
     */
    public function wishlistItems(): HasMany
    {
        return $this->hasMany(WishlistItem::class);
    }
}
