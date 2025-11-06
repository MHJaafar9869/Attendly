<?php

namespace Modules\Core\Traits;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Pagination\AbstractPaginator;
use Modules\Core\DTO\RepositoryResponseDto;
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
        mixed $data = null,
        int $status = 200,
        array $extra = [],
        bool $paginate = false,
    ): JsonResponse {
        $response = [
            'success' => $success,
            'status_code' => $status,
            'message' => $message,
        ];

        if ($data !== null) {
            if ($paginate && $this->isPaginatable($data)) {
                $response = array_merge($response, $this->formatPaginatedData($data));
            } else {
                $response['data'] = $data;
            }
        }

        $response = array_merge($response, $extra);

        return response()->json($response, $status);
    }

    /**
     * Check if data can be paginated
     */
    private function isPaginatable(mixed $data): bool
    {
        return $data instanceof LengthAwarePaginator
            || $data instanceof AbstractPaginator
            || $data instanceof ResourceCollection;
    }

    /**
     * Format paginated data with meta and links
     */
    private function formatPaginatedData(
        LengthAwarePaginator | AbstractPaginator | ResourceCollection $paginator
    ): array {
        if ($paginator instanceof ResourceCollection) {
            $paginator = $paginator->resource;
        }

        return [
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
     * Paginated response
     */
    protected function respondWithPagination(
        LengthAwarePaginator | AbstractPaginator | ResourceCollection $data,
        string $message = 'Data retrieved successfully',
        int $status = 200,
        array $extra = []
    ): JsonResponse {
        return $this->respond(
            success: true,
            message: $message,
            data: $data,
            status: $status,
            extra: $extra,
            paginate: true
        );
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

    /**
     * Respond with DTO success - returns full structure
     */
    protected function respondDtoSuccess(RepositoryResponseDto $responseDto): JsonResponse
    {
        return response()->json($responseDto->toArray(), $responseDto->statusCode);
    }

    /**
     * Respond with DTO error - returns full structure
     */
    protected function respondDtoError(RepositoryResponseDto $responseDto): JsonResponse
    {
        return response()->json($responseDto->toArray(), $responseDto->statusCode);
    }
}
