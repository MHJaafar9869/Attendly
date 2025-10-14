<?php

namespace App\Traits;

trait ResponseArray
{
    protected function respond(
        bool $success,
        ?string $message = null,
        ?int $status = 200,
        ?array $data = null
    ): array {
        $response = [
            'success' => $success,
            'message' => $message,
            'status' => $status,
        ];

        if ($data !== []) {
            $response['data'] = $data;
        }

        return $response;
    }

    protected function arrayResponseSuccess(
        string $message = 'Request has been successful',
        int $status = 200,
        bool $success = true,
        ?array $data = null
    ) {
        return $this->respond($success, $message, $status, $data);
    }

    protected function arrayResponseError(
        string $message = 'Request Failed',
        int $status = 400,
        bool $success = false
    ) {
        return $this->respond($success, $message, $status);
    }
}
