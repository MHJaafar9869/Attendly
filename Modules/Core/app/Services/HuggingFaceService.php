<?php

namespace Modules\Core\Services;

use Illuminate\Support\Facades\Http;

class HuggingFaceService
{
    protected string $token;

    protected string $baseUrl;

    public function __construct()
    {
        $this->token = config('services.huggingface.key');
        $this->baseUrl = config('services.huggingface.url');
    }

    public function query(string $model, array $payload = [])
    {
        $response = Http::withToken($this->token)
            ->timeout(30)
            ->post("{$this->baseUrl}{$model}", $payload);

        return $response->json();
    }
}
