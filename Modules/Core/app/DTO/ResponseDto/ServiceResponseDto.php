<?php

declare(strict_types=1);

namespace Modules\Core\DTO\ResponseDto;

use Illuminate\Http\Resources\Json\ResourceCollection;

final readonly class ServiceResponseDto
{
    public function __construct(
        public bool $success,
        public string $message,
        public int $statusCode = 200,
        public ResourceCollection | array | null $data = null,
        public mixed $token = null
    ) {}

    public static function success(
        string $message = 'Operation successful',
        ResourceCollection | array | null $data = null,
        int $statusCode = 200
    ): self {
        return new self(
            success: true,
            message: $message,
            statusCode: $statusCode,
            data: $data,
        );
    }

    public static function error(
        string $message = 'Operation failed',
        int $statusCode = 400,
        ResourceCollection | array | null $data = null
    ): self {
        return new self(
            success: false,
            message: $message,
            statusCode: $statusCode,
            data: $data
        );
    }

    public static function response(RepositoryResponseDto $responseDto): self
    {
        $props = [
            'success' => $responseDto->success,
            'message' => $responseDto->message,
            'statusCode' => $responseDto->statusCode,
        ];

        if ($responseDto->success && $responseDto->data !== null) {
            $props['data'] = $responseDto->data;
        } elseif ($responseDto->success && $responseDto->data === null) {
            $props['data'] = [];
        }

        if ($responseDto->token !== null) {
            $props['token'] = $responseDto->token;
        }

        return new self(...$props);
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function isError(): bool
    {
        return ! $this->success;
    }

    /**
     * Immutable setter for data
     */
    public function setData(mixed $data): self
    {
        return new self(
            success: $this->success,
            message: $this->message,
            statusCode: $this->statusCode,
            data: $data,
        );
    }

    /**
     * Immutable setter for message
     */
    public function setMessage(string $message): self
    {
        return new self(
            success: $this->success,
            message: $message,
            statusCode: $this->statusCode,
            data: $this->data,
        );
    }

    /**
     * Immutable setter for message
     */
    public function setStatus(int $status): self
    {
        return new self(
            success: $this->success,
            message: $this->message,
            statusCode: $status,
            data: $this->data,
        );
    }

    public function setToken(mixed $token): self
    {
        return new self(
            success: $this->success,
            message: $this->message,
            statusCode: $this->statusCode,
            data: $this->data,
            token: $token
        );
    }
}
