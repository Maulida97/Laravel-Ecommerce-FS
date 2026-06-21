<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class VariantAttributeValue extends Model
{
    protected $fillable = [
        'variant_attribute_id',
        'value',
        'color_code',
    ];

    /**
     * Parent attribute relation.
     */
    public function attribute(): BelongsTo
    {
        return $this->belongsTo(VariantAttribute::class, 'variant_attribute_id');
    }

    /**
     * Associated product variants.
     */
    public function variants(): BelongsToMany
    {
        return $this->belongsToMany(
            ProductVariant::class,
            'product_variant_combinations',
            'variant_attribute_value_id',
            'product_variant_id'
        );
    }
}
