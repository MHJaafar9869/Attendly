<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\Traits\ResponseJson;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Throwable;

class ApiExceptionHandler extends Exception
{
    use ResponseJson;

    public function handleApiException(Throwable $e)
    {
        if ($e instanceof ModelNotFoundException) {
            return $this->respondError('Model not found', 400);
        }

        if ($e instanceof AuthenticationException) {
            return $this->respondError('Unauthenticated', 401);
        }

        if ($e instanceof AuthorizationException) {
            return $this->respondError('Action is unauthorized', 403);
        }

        if ($e instanceof NotFoundHttpException) {
            return $this->respondError('Not found: ' . $e->getMessage(), 404);
        }

        if ($e instanceof MethodNotAllowedHttpException) {
            return $this->respondError('Method not allowed', 405);
        }

        if ($e instanceof ValidationException) {
            return $this->respondError('Failed validation', 422);
        }

        if ($e instanceof RouteNotFoundException) {
            return $this->respondError('Route not found', 500);
        }

        return $this->respondError('Internal server error', 500);
    }
}
