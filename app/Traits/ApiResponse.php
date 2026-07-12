<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    // ─────────────────────────────────────
    // Success Response
    // ─────────────────────────────────────
    protected function success(
        mixed $data = null,
        string $message = 'Success',
        int $code = 200
    ): JsonResponse {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    // ─────────────────────────────────────
    // Error Response
    // ─────────────────────────────────────
    protected function error(
        string $message = 'Error',
        int $code = 400,
        mixed $errors = null
    ): JsonResponse {
        $response = [
            'status' => 'error',
            'message' => $message,
        ];

        if ($errors) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $code);
    }

    // ─────────────────────────────────────
    // Paginated Response
    // ─────────────────────────────────────
    /**
     * @param  mixed  $paginator  The paginator instance (used for meta: current_page, last_page, etc.)
     * @param  iterable|null  $data  Optional pre-transformed data to use instead of
     *                               $paginator->items(). Always pass this when the
     *                               underlying models shouldn't be serialized raw
     *                               (e.g. anything touching Admin, which has no
     *                               $hidden password field) — see Phase8 API-01/API-10.
     */
    protected function paginated(
        mixed $paginator,
        string $message = 'Success',
        ?iterable $data = null
    ): JsonResponse {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data !== null ? collect($data)->values()->all() : $paginator->items(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
        ]);
    }
}
