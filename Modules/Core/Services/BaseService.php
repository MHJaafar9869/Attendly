<?php

declare(strict_types=1);

namespace Modules\Core\Services;

use Exception;
use Modules\Core\DTO\ResponseDto\ServiceResponseDto;
use Throwable;

readonly class BaseService
{
    public function __construct(
        // ...
    ) {}

    public function getErrorResponse(Exception | Throwable $error, int $statusCode = 500, string $msg = 'Error occurred. Please try again'): ServiceResponseDto
    {
        logger()->error('Failed with error: ' . $error->getMessage());

        return app()->environment('local')
            ? ServiceResponseDto::error('Failed with error: ' . $error->getMessage(), $statusCode)
            : ServiceResponseDto::error($msg, 500);
    }
}
