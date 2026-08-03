<?php

namespace App\Models;

use App\Enums\DeliveryAssignmentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeliveryAssignment extends Model
{
    protected $fillable = [
        'order_id',
        'driver_user_id',
        'status',
        'assigned_at',
        'delivered_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'status' => DeliveryAssignmentStatus::class,
            'assigned_at' => 'datetime',
            'delivered_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'driver_user_id');
    }
}
