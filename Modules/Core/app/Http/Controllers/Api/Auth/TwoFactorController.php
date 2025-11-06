<?php

declare(strict_types=1);

namespace Modules\Core\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Core\Http\Requests\Auth\ConfirmTwoFactorCodeRequest;
use Modules\Core\Repositories\User\UserRepositoryInterface;
use Modules\Core\Traits\ResponseJson;

class TwoFactorController extends Controller
{
    use ResponseJson;

    public function __construct(
        protected UserRepositoryInterface $userRepo,
    ) {}

    private function handle(string $repoMethod, $user, ...$args): JsonResponse
    {
        try {
            $response = $this->userRepo->{$repoMethod}($user, ...$args);
        } catch (Exception $e) {
            logger()->error("2FA {$repoMethod} failure", ['error' => $e->getMessage()]);

            $message = app()->environment('local')
                ? "Error: {$e->getMessage()}"
                : 'Something went wrong with 2FA';

            return $this->respondError($message, 400);
        }

        if ($response['success'] === true) {
            if ($response['is_token'] === true) {
                return $this->respondWithToken($response['data'], message: $response['message']);
            }

            if (isset($response['data'])) {
                return $this->respondWithData($response['data'], $response['message']);
            }

            return $this->respondSuccess($response['message']);
        }

        return $this->respondError($response['message'], $response['status']);
    }

    public function enable(Request $request): JsonResponse
    {
        return $this->handle('enable2FA', $request->user());
    }

    public function disable(Request $request): JsonResponse
    {
        return $this->handle('disable2FA', $request->user());
    }

    public function setup(Request $request): JsonResponse
    {
        return $this->handle('setup2FA', $request->user());
    }

    public function confirm(ConfirmTwoFactorCodeRequest $request): JsonResponse
    {
        return $this->handle('confirm2FA', $request->user(), $request->validated('code'));
    }
}
