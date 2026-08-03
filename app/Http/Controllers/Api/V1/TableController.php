<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\TableStatus;
use App\Http\Controllers\Api\Concerns\RespondsWithJson;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\TableStatusRequest;
use App\Models\DiningTable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TableController extends Controller
{
    use RespondsWithJson;

    public function index(Request $request): JsonResponse
    {
        $tables = DiningTable::query()
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')->toString()))
            ->when($request->filled('area'), fn ($query) => $query->where('area', $request->string('area')->toString()))
            ->orderBy('sort_order')
            ->orderBy('code')
            ->get();

        $stats = [
            'total' => DiningTable::query()->count(),
            'available' => DiningTable::query()->where('status', TableStatus::Available)->count(),
            'occupied' => DiningTable::query()->where('status', TableStatus::Occupied)->count(),
            'reserved' => DiningTable::query()->where('status', TableStatus::Reserved)->count(),
        ];

        return $this->success([
            'stats' => $stats,
            'tables' => $tables->map(fn (DiningTable $table) => $this->serialize($table))->values()->all(),
        ]);
    }

    public function updateStatus(TableStatusRequest $request, DiningTable $table): JsonResponse
    {
        $table->update(['status' => TableStatus::from($request->validated('status'))]);

        return $this->success($this->serialize($table));
    }

    /**
     * @return array<string, mixed>
     */
    protected function serialize(DiningTable $table): array
    {
        return [
            'id' => $table->id,
            'code' => $table->code,
            'name' => $table->name,
            'area' => $table->area,
            'capacity' => $table->capacity,
            'status' => $table->status->value,
            'status_label' => $table->status->label(),
        ];
    }
}
