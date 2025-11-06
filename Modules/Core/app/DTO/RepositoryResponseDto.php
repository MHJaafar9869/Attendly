<?php

declare(strict_types=1);

namespace Modules\Core\DTO;

use RuntimeException;

final readonly class RepositoryResponseDto
{
    public function __construct(
        public bool $success,
        public string $message,
        public int $statusCode = 200,
        public mixed $data = null,
        public bool $isToken = false
    ) {}

    public static function success(
        string $message = 'Operation successful',
        mixed $data = null,
        int $statusCode = 200
    ): self {
        return new self(
            success: true,
            message: $message,
            statusCode: $statusCode,
            data: $data
        );
    }

    public static function error(
        string $message = 'Operation failed',
        int $statusCode = 400,
        mixed $data = null
    ): self {
        return new self(
            success: false,
            message: $message,
            statusCode: $statusCode,
            data: $data
        );
    }

    public static function withToken(
        string $message = 'Authentication successful',
        mixed $data = null,
        int $statusCode = 200
    ): self {
        return new self(
            success: true,
            message: $message,
            statusCode: $statusCode,
            data: $data,
            isToken: true
        );
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function isError(): bool
    {
        return ! $this->success;
    }

    public function hasData(): bool
    {
        return $this->data !== null;
    }

    /**
     * Get data or throw exception if not successful
     */
    public function getDataOrFail(): mixed
    {
        if ($this->isError()) {
            throw new RuntimeException($this->message);
        }

        return $this->data;
    }

    /**
     * Get data or return default value
     */
    public function getDataOr(mixed $default = null): mixed
    {
        return $this->isSuccess()
            ? $this->data
            : $default;
    }

    /**
     * Convert to array for JSON responses
     */
    public function toArray(): array
    {
        $response = [
            'success' => $this->success,
            'status_code' => $this->statusCode,
            'message' => $this->message,
        ];

        if ($this->hasData()) {
            $response['data'] = $this->data;
        }

        if ($this->isToken) {
            $response['is_token'] = true;
        }

        return $response;
    }

    /**
     * Immutable setter for data
     */
    public function withData(mixed $data): self
    {
        return new self(
            success: $this->success,
            message: $this->message,
            statusCode: $this->statusCode,
            data: $data,
            isToken: $this->isToken
        );
    }

    /**
     * Immutable setter for message
     */
    public function withMessage(string $message): self
    {
        return new self(
            success: $this->success,
            message: $message,
            statusCode: $this->statusCode,
            data: $this->data,
            isToken: $this->isToken
        );
    }

    /**
     * Immutable setter for message
     */
    public function withStatus(int $status): self
    {
        return new self(
            success: $this->success,
            message: $this->message,
            statusCode: $status,
            data: $this->data,
            isToken: $this->isToken
        );
    }
}
