<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecipeItem extends Model
{
    protected $fillable = [
        'product_id',
        'ingredient_name',
        'quantity',
        'unit',
        'cost_per_unit',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:3',
            'cost_per_unit' => 'decimal:2',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
