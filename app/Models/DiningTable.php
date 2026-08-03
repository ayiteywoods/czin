<?php

namespace App\Models;

use App\Enums\TableSize;
use App\Enums\TableStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DiningTable extends Model
{
    protected $fillable = [
        'code',
        'name',
        'area',
        'capacity',
        'size',
        'price',
        'status',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'capacity' => 'integer',
            'price' => 'decimal:2',
            'size' => TableSize::class,
            'status' => TableStatus::class,
            'sort_order' => 'integer',
        ];
    }

    public function scopeFiltered(Builder $query, ?string $search = null, ?string $status = null, ?string $area = null): Builder
    {
        return $query
            ->when(filled($search), function (Builder $builder) use ($search) {
                $builder->where(function (Builder $inner) use ($search) {
                    $inner->where('code', 'like', '%'.$search.'%')
                        ->orWhere('name', 'like', '%'.$search.'%')
                        ->orWhere('area', 'like', '%'.$search.'%');
                });
            })
            ->when(filled($status), fn (Builder $builder) => $builder->where('status', $status))
            ->when(filled($area), fn (Builder $builder) => $builder->where('area', $area));
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function capacityLabel(): string
    {
        return $this->capacity.' Person';
    }
}
