<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VariantAttribute extends Model
{
    protected $fillable = [
        'name',
    ];

    /**
     * Values of this attribute.
     */
    public function values(): HasMany
    {
        return $this->hasMany(VariantAttributeValue::class);
    }
}
