<?php

namespace App\Models;

use App\Enums\ReservationStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reservation extends Model
{
    protected $fillable = [
        'dining_table_id',
        'user_id',
        'guest_name',
        'guest_phone',
        'guest_email',
        'party_size',
        'reserved_at',
        'duration_minutes',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'party_size' => 'integer',
            'reserved_at' => 'datetime',
            'duration_minutes' => 'integer',
            'status' => ReservationStatus::class,
        ];
    }

    public function diningTable(): BelongsTo
    {
        return $this->belongsTo(DiningTable::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
