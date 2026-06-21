<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id',
        'variant_name',
        'sku',
        'price_adjustment',
        'stock_quantity',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Product relation.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Varian attribute value combinations.
     */
    public function combinations(): BelongsToMany
    {
        return $this->belongsToMany(
            VariantAttributeValue::class,
            'product_variant_combinations',
            'product_variant_id',
            'variant_attribute_value_id'
        );
    }
}
