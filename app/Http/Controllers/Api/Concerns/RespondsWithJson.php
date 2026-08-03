<?php

namespace App\Http\Controllers\Api\Concerns;

use Illuminate\Http\JsonResponse;

trait RespondsWithJson
{
    /**
     * @param  mixed  $data
     * @param  array<string, mixed>  $meta
     */
    protected function success(mixed $data, array $meta = [], int $status = 200): JsonResponse
    {
        return response()->json([
            'data' => $data,
            'meta' => (object) $meta,
        ], $status);
    }

  /**
     * @param  array<string, mixed>|null  $errors
     */
    protected function error(string $message, int $status = 400, ?array $errors = null): JsonResponse
    {
        $payload = ['message' => $message];

        if ($errors !== null) {
            $payload['errors'] = $errors;
        }

        return response()->json($payload, $status);
    }
}
