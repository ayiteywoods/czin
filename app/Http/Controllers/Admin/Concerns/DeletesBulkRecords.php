<?php

namespace App\Http\Controllers\Admin\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

trait DeletesBulkRecords
{
    /**
     * @param  class-string<Model>  $modelClass
     * @param  callable(Builder): Builder|null  $scope
     * @param  callable(Model): bool|null  $beforeDelete  Return false to skip.
     */
    protected function destroySelected(
        Request $request,
        string $modelClass,
        string $label,
        ?callable $scope = null,
        ?callable $beforeDelete = null,
        int $max = 100,
    ): RedirectResponse {
        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1', 'max:'.$max],
            'ids.*' => ['integer'],
        ]);

        /** @var Collection<int, int> $ids */
        $ids = collect($validated['ids'])
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        $query = $modelClass::query()->whereIn('id', $ids);

        if ($scope) {
            $query = $scope($query) ?? $query;
        }

        $records = $query->get();
        $deleted = 0;
        $skipped = 0;

        foreach ($records as $record) {
            if ($beforeDelete && $beforeDelete($record) === false) {
                $skipped++;

                continue;
            }

            $record->delete();
            $deleted++;
        }

        if ($deleted === 0) {
            return back()->with(
                'error',
                $skipped > 0
                    ? "No {$label} could be deleted."
                    : "Select at least one {$label} to delete."
            );
        }

        $message = $deleted === 1
            ? "1 {$label} deleted."
            : "{$deleted} {$label} deleted.";

        if ($skipped > 0) {
            $message .= " {$skipped} skipped.";
        }

        return back()->with('success', $message);
    }
}
