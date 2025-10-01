<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ResponseJson
{
    /**
     * Build a general JSON response.
     */
    protected function respond(
        bool $success,
        ?string $message = null,
        mixed $data = null,
        int $status = 200,
        array $extra = [],
        bool $paginate = false,
    ): JsonResponse {
        $response = [
            'success' => $success,
            'message' => $message,
        ];

        if ($data !== null) {
            if ($paginate && $data instanceof \Illuminate\Http\Resources\Json\ResourceCollection) {
                $collection = $data->resource;
                $response['data'] = $collection->items();
                $response['meta'] = [
                    'total' => $collection->total(),
                    'per_page' => $collection->perPage(),
                    'current_page' => $collection->currentPage(),
                    'last_page' => $collection->lastPage(),
                ];
            } elseif ($paginate && $data instanceof \Illuminate\Pagination\AbstractPaginator) {
                /** @var \Illuminate\Pagination\LengthAwarePaginator $paginator */
                $paginator = $data;
                $response['data'] = $paginator->items();
                $response['meta'] = [
                    'total' => $paginator->total(),
                    'per_page' => $paginator->perPage(),
                    'current_page' => $paginator->currentPage(),
                    'last_page' => $paginator->lastPage(),
                ];
            } else {
                $response['data'] = $data;
            }
        }

        $response = array_merge($response, $extra);

        return response()->json($response, $status);
    }

    /**
     * Success without data.
     */
    protected function respondSuccess(string $message = 'Operation completed successfully', int $status = 200): JsonResponse
    {
        return $this->respond(true, $message, null, $status);
    }

    /**
     * Error without data.
     */
    protected function respondError(string $message = 'Something went wrong', int $status = 400): JsonResponse
    {
        return $this->respond(false, $message, null, $status);
    }

    /**
     * Success with data.
     */
    protected function respondWithData(mixed $data, string $message = 'Data retrieved successfully', int $status = 200): JsonResponse
    {
        return $this->respond(true, $message, $data, $status);
    }

    /**
     * Success with paginated data.
     */
    protected function respondWithPaginatedData(mixed $data, string $message = 'Data retrieved successfully', int $status = 200, bool $paginate = true): JsonResponse
    {
        return $this->respond(true, $message, $data, $status, paginate: $paginate);
    }

    /**
     * Error with optional data.
     */
    protected function respondWithErrorData(string $message = 'Failed to retrieve data', mixed $data = null, int $status = 400): JsonResponse
    {
        return $this->respond(false, $message, $data, $status);
    }

    /**
     * Unauthorized or expired token.
     */
    protected function respondUnauthorized(string $message = 'Unauthorized', int $status = 401): JsonResponse
    {
        return $this->respond(false, $message, null, $status);
    }
}
