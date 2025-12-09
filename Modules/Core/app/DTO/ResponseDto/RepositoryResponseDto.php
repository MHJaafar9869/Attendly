<?php

declare(strict_types=1);

namespace Modules\Core\DTO\ResponseDto;

final readonly class RepositoryResponseDto
{
    public function __construct(
        public bool $success,
        public string $message,
        public int $statusCode = 200,
        public mixed $data = null,
        public mixed $token = null
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
            token: $this->token
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
            token: $this->token
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
            token: $this->token
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
