<?php

namespace App\Models;

use App\Enums\LoyaltyTransactionType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoyaltyTransaction extends Model
{
    protected $fillable = [
        'loyalty_account_id',
        'order_id',
        'points',
        'type',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'points' => 'integer',
            'type' => LoyaltyTransactionType::class,
        ];
    }

    public function loyaltyAccount(): BelongsTo
    {
        return $this->belongsTo(LoyaltyAccount::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
