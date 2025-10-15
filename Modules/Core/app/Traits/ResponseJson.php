<?php

namespace Modules\Core\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Core\Models\User;
use Modules\Core\Transformers\User\UserResource;

trait ResponseJson
{
    /**
     * Build a general JSON response.
     */
    protected function respond(
        bool $success,
        ?string $message = null,
        ?array $data = null,
        int $status = 200,
        array $extra = [],
        bool $paginate = false,
    ): JsonResponse {
        $response = [
            'success' => $success,
            'message' => $message,
        ];

        if ($data !== null) {
            if ($paginate && ($data instanceof AbstractPaginator || $data instanceof ResourceCollection)) {
                /** @var LengthAwarePaginator $paginator */
                $paginator = $data;
                $response[] = [
                    'data' => $paginator->items(),
                    'meta' => [
                        'current_page' => $paginator->currentPage(),
                        'per_page' => $paginator->perPage(),
                        'from' => $paginator->firstItem(),
                        'to' => $paginator->lastItem(),
                        'total' => $paginator->total(),
                        'last_page' => $paginator->lastPage(),
                    ],
                    'links' => [
                        'first' => $paginator->url(1),
                        'last' => $paginator->url($paginator->lastPage()),
                        'prev' => $paginator->previousPageUrl(),
                        'next' => $paginator->nextPageUrl(),
                    ],
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
    protected function respondWithErrorData(string $message = 'Failed to retrieve data', ?array $data = null, int $status = 400): JsonResponse
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

    protected function respondWithToken(string $token, ?User $user = null, string $message = 'Token retrieved successfully'): JsonResponse
    {
        $data = [
            'authorization' => [
                'token_type' => 'bearer',
                'expires_in_sec' => jwtGuard()->factory()->getTTL() * 60,
                'token' => $token,
            ],
            'user' => new UserResource($user ?? jwtGuard()->user()),
        ];

        return $this->respond(true, $message, $data);
    }
}
