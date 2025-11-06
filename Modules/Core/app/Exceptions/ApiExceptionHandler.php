<?php

declare(strict_types=1);

namespace Modules\Core\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Modules\Core\Traits\ResponseJson;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Throwable;

final readonly class ApiExceptionHandler
{
    use ResponseJson;

    public function handleApiException(Throwable $th)
    {
        $exceptions = [
            AuthenticationException::class => ['Unauthenticated', 401],
            AuthorizationException::class => ['Action is unauthorized', 403],
            ModelNotFoundException::class => ['Model not found', 404],
            RouteNotFoundException::class => ['Route not found', 404],
            NotFoundHttpException::class => ['Not found', 404],
            MethodNotAllowedHttpException::class => ['Method not allowed', 405],
            ValidationException::class => ['Failed validation', 422],
        ];

        foreach ($exceptions as $ex => [$message, $code]) {
            if ($th instanceof $ex) {
                if (app()->environment('local')) {
                    return $this->respondError('Error: ' . $th->getMessage(), $code);
                }

                return $this->respondError($message, $code);
            }
        }

        if (app()->environment('local')) {
            return $this->respondError('Failed with error: ' . $th->getMessage(), 500);
        }

        return $this->respondError('Error Occurred, Please try again', 500);
    }
}
