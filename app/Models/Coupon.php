<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = [
        'code', 'type', 'value', 'is_active', 'requires_subscription', 'usage_limit', 'used_count', 'expires_at'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'requires_subscription' => 'boolean',
        'expires_at' => 'datetime',
        'value' => 'float',
    ];

    public function isValid()
    {
        if (!$this->is_active) return false;
        if ($this->expires_at && $this->expires_at->isPast()) return false;
        if ($this->usage_limit && $this->used_count >= $this->usage_limit) return false;
        return true;
    }

    public function calculateDiscount($subtotal)
    {
        if (!$this->isValid()) return 0;

        if ($this->type === 'percent') {
            return ($subtotal * $this->value) / 100;
        }

        return min($this->value, $subtotal);
    }
}
