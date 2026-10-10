<?php

namespace App\Models;

use App\Enums\PromotionType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Promotion extends Model
{
    protected $fillable = [
        'name',
        'type',
        'value',
        'starts_at',
        'ends_at',
        'category_id',
        'product_id',
        'is_active',
        'days_of_week',
        'start_time',
        'end_time',
    ];

    protected function casts(): array
    {
        return [
            'type' => PromotionType::class,
            'value' => 'decimal:2',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'is_active' => 'boolean',
            'days_of_week' => 'array',
            'category_id' => 'integer',
            'product_id' => 'integer',
        ];
    }

    public function appliesToLabel(): string
    {
        if ($this->product_id) {
            return $this->product?->name
                ? 'Product: '.$this->product->name
                : 'Product #'.$this->product_id;
        }

        if ($this->category_id) {
            return $this->category?->name
                ? 'Category: '.$this->category->name
                : 'Category #'.$this->category_id;
        }

        return 'Not targeted';
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
