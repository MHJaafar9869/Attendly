<?php

declare(strict_types=1);

namespace Modules\Core\Http\Controllers\Api\Auth;

use App\DTO\Auth\UserImageDto;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Modules\Core\DTO\Auth\LoginUserDto;
use Modules\Core\DTO\Auth\RegisterUserDto;
use Modules\Core\DTO\Auth\ResetPasswordDto;
use Modules\Core\Http\Requests\Auth\ForgotPasswordRequest;
use Modules\Core\Http\Requests\Auth\LoginRequest;
use Modules\Core\Http\Requests\Auth\RegisterRequest;
use Modules\Core\Http\Requests\Auth\ResetPasswordRequest;
use Modules\Core\Http\Requests\Auth\StoreProfilePictureRequest;
use Modules\Core\Http\Requests\Auth\VerifyOtpRequest;
use Modules\Core\Models\User;
use Modules\Core\Repositories\User\UserRepositoryInterface;
use Modules\Core\Services\UserServices\AuthService;
use Modules\Core\Traits\ResponseJson;
use Modules\Core\Transformers\User\UserResource;

final class AuthController extends Controller
{
    use ResponseJson;

    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct(
        protected UserRepositoryInterface $userRepo,
        protected AuthService $authService,
    ) {
        $this->middleware('auth-user', ['only' => ['me', 'logout', 'refresh', 'storeProfileImage']]);
    }

    /**
     * Handle login request to the application.
     * POST /api/v1/auth/login { email, password }
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $dto = LoginUserDto::fromRequest($request->validated());

        $response = $this->authService->login($dto);

        return $this->respondDto($response);
    }

    /**
     * Handle register request to the application.
     * POST /api/v1/auth/register { first_name, last_name, email, password }
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $dto = RegisterUserDto::fromRequest($request->validated());

        $response = $this->authService->register($dto);

        return $this->respondDto($response);
    }

    /**
     * Handle OTP verification request to the application.
     * POST /api/v1/auth/{slug}/verfiy-otp { otp }
     */
    public function verifyOtp(string $userSlug, VerifyOtpRequest $request): JsonResponse
    {
        $response = $this->authService->verifyOtp($userSlug, $request->validated('otp'));

        return $this->respondDto($response);
    }

    /**
     * Get the authenticated User.
     * GET /api/v1/auth/me
     */
    public function me(): JsonResponse
    {
        $user = Cache::flexible(
            key: 'users:'.sanctumUser()->id,
            ttl: [30, 60],
            callback: fn () => sanctumUser()->load(
                [
                    'images:id,image_path,image_url,type',
                    'roles:id,name',
                    'roles.permissions:id,name',
                ]
            )
        );

        $message = \sprintf('User %s data retrieved', $user->first_name);

        return $this->respondWithData(UserResource::make($user), $message);
    }

    /**
     * Log the user out (Invalidate the token).
     * POST /api/v1/auth/logout
     */
    public function logout(): JsonResponse
    {
        try {
            /** @var User $user */
            $user = sanctumUser();
            $user->update(['is_logged_in' => false]);
            $user?->currentAccessToken()->delete();
        } catch (Exception $e) {
            return app()->environment('local')
                ? $this->respondError('Failed due: '.$e->getMessage(), 500)
                : $this->respondError('Failed to logout, please try again later.', 500);
        }

        return $this->respondSuccess('Successfully logged out');
    }

    /**
     * Handle forgot password request to the application.
     * POST /api/v1/auth/forgot-password { email }
     */
    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $email = $request->validated();

        $dto = $this->authService->forgotPassword($email);

        return $this->respondDto($dto);
    }

    /**
     * Handle reset password request to the application.
     * POST /api/v1/auth/reset-password { email, password, token }
     */
    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $dto = ResetPasswordDto::fromRequest($request->validated());

        $dto = $this->authService->resetPassword($dto);

        return $this->respondDto($dto);
    }

    /**
     * Handle user image upload request to the application.
     * POST /api/v1/auth/{user:slug}/upload-image { image, type }
     */
    public function storeUserImage(StoreProfilePictureRequest $request, string $userId): JsonResponse
    {
        $imageDto = UserImageDto::fromRequest($request->validated(), $userId);

        $response = $this->authService->uploadUserImage($imageDto);

        return $this->respondDto($response);
    }
}
