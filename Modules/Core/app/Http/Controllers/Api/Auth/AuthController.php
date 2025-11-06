<?php

declare(strict_types=1);

namespace Modules\Core\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
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
use Tymon\JWTAuth\Exceptions\JWTException;

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
     * Get a JWT via given credentials.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $dto = $this->userRepo->login($request->validated());

            return $dto->isSuccess()
                ? $this->respondDtoSuccess($dto)
                : $this->respondDtoError($dto);

        } catch (Exception $e) {
            return $this->respondError('Login failed. Please try again later.', 500);
        }
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $dto = $this->userRepo->register($request->validated());

            return $dto->isSuccess()
                ? $this->respondDtoSuccess($dto)
                : $this->respondDtoError($dto);

        } catch (Exception $e) {
            return $this->respondError('Registration failed. Please try again later.', 500);
        }
    }

    public function verifyOtp(VerifyOtpRequest $request): JsonResponse
    {
        try {
            $user = jwtGuard()->user();

            if (! $user) {
                return $this->respondUnauthorized();
            }

            $dto = $this->userRepo->verifyOtp(
                userId: $user->id,
                otp: $request->input('otp'),
                remember: $request->boolean('remember', false)
            );

            return $dto->isSuccess()
                ? $this->respondDtoSuccess($dto)
                : $this->respondDtoError($dto);

        } catch (Exception $e) {
            return $this->respondError('OTP verification failed. Please try again later.', 500);
        }
    }

    /**
     * Get the authenticated User.
     */
    public function me(): JsonResponse
    {
        $user = jwtGuard()->user()->load([
            'images:id,imageable_id,imageable_type,image_path,image_url,type',
            'roles:id,name',
            'roles.permissions:id,name',
        ]);

        return $this->respondWithData(
            UserResource::make($user),
            sprintf('User %s data retrieved successfully', ucfirst($user->first_name))
        );
    }

    /**
     * Log the user out (Invalidate the token).
     */
    public function logout(): JsonResponse
    {
        try {
            jwtGuard()->logout();

            return $this->respondSuccess('Successfully logged out');
        } catch (JWTException $e) {
            return app()->environment('local')
                ? $this->respondError('Failed due: ' . $e->getMessage(), 500)
                : $this->respondError('Failed to logout, please try again later.', 500);
        }
    }

    /**
     * Refresh a token.
     */
    public function refresh(): JsonResponse
    {
        try {
            $newToken = jwtGuard()->refresh();

            return $this->respondWithToken($newToken);
        } catch (JWTException $e) {
            return $this->respondError('Could not refresh token', 500);
        }
    }

    public function forgotPassword(ForgotPasswordRequest $request)
    {
        $validated = $request->validated();
        $dto = $this->userRepo->forgotPassword($validated);

        return $dto->isSuccess()
            ? $this->respondDtoSuccess($dto)
            : $this->respondDtoError($dto);
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        $validated = $request->validated();

        try {
            $dto = $this->userRepo->resetPassword(
                $validated['email'],
                $validated['password'],
                $validated['token']
            );
        } catch (Exception $e) {
            return $this->respondError("Failed to reset password, with error(s): {$e->getMessage()}");
        }

        return $dto->isSuccess()
            ? $this->respondDtoSuccess($dto)
            : $this->respondDtoError($dto);
    }

    public function storeProfileImage(StoreProfilePictureRequest $request, User $user): JsonResponse
    {
        try {
            $request->validated();
            $dto = $this->authService->uploadUserImage(
                $request->file('image'),
                $user->id,
                $user->slug_name,
                $request->string('type')->lower()->toString()
            );
        } catch (Exception $e) {
            return app()->environment('local')
               ? $this->respondError('Failed due: ' . $e->getMessage(), 500)
               : $this->respondError('Failed to upload profile image, please try again later.', 500);
        }

        return $dto->isSuccess()
            ? $this->respondDtoSuccess($dto)
            : $this->respondDtoError($dto);
    }
}
