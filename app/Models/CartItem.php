<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    protected $fillable = [
        'cart_id',
        'product_id',
        'product_variant_id',
        'quantity',
        'unit_price',
        'special_request',
        'reserved_until',
    ];

    protected function casts(): array
    {
        return [
            'cart_id' => 'integer',
            'product_id' => 'integer',
            'product_variant_id' => 'integer',
            'quantity' => 'integer',
            'unit_price' => 'decimal:2',
            'reserved_until' => 'datetime',
        ];
    }

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function optionLabel(): ?string
    {
        $label = $this->variant?->displayLabel();

        if (filled($this->special_request)) {
            $custom = 'Custom: '.$this->special_request;

            return $label ? "{$label} · {$custom}" : $custom;
        }

        return $label;
    }

    public function lineTotal(): float
    {
        return (float) ($this->unit_price * $this->quantity);
    }
}
