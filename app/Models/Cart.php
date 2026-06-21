<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
{
    protected $fillable = [
        'user_id',
        'session_id',
    ];

    /**
     * User relation.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Cart items.
     */
    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Calculate cart subtotal.
     */
    public function getSubtotal(): float
    {
        return (float) $this->items->sum(function (CartItem $item) {
            return $item->quantity * $item->unit_price;
        });
    }

    /**
     * Get total item count in cart.
     */
    public function getItemCount(): int
    {
        return (int) $this->items->sum('quantity');
    }
}
