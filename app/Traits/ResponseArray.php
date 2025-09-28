<?php

namespace App\Traits;

trait ResponseArray
{
    protected function respond(
        bool $success,
        string $message,
        ?string $status = null,
        mixed $data = []
    ): array {
        return [
            'success' => $success,
            'message' => $message,
            'status' => $status,
            'data' => $data,
        ];
    }

    protected function arrayResponseSuccess(
        string $message = 'Request has been successful',
        string $status = 'success',
        bool $success = true,
        mixed $data = null
    ) {
        return $this->respond($success, $message, $status, $data);
    }

    protected function arrayResponseError(
        string $message = 'Request Failed',
        string $status = 'failed',
        bool $success = false
    ) {
        return $this->respond($success, $message, $status);
    }
}
