<?php

declare(strict_types=1);

namespace Modules\Core\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Core\Http\Requests\Auth\ForgotPasswordRequest;
use Modules\Core\Http\Requests\Auth\LoginRequest;
use Modules\Core\Http\Requests\Auth\RegisterRequest;
use Modules\Core\Http\Requests\Auth\ResetPasswordRequest;
use Modules\Core\Repositories\User\UserRepositoryInterface;
use Modules\Core\Traits\OTP;
use Modules\Core\Traits\ResponseJson;
use Modules\Core\Transformers\User\UserResource;
use Tymon\JWTAuth\Exceptions\JWTException;

final class AuthController extends Controller
{
    use OTP;
    use ResponseJson;

    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct(
        protected UserRepositoryInterface $userRepo
    ) {
        $this->middleware('auth-user', ['only' => ['me', 'logout', 'refresh']]);
    }

    /**
     * Get a JWT via given credentials.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        return $this->handleAuth($request->validated(), 'login', 'Login Failed');
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        return $this->handleAuth($request->validated(), 'register', 'Registeration Failed');
    }

    public function handleAuth(array $data, string $method, string $errMessage): JsonResponse
    {
        try {
            $response = $this->userRepo->{$method}($data);
        } catch (Exception $e) {
            return $this->respondError($errMessage);
        }

        if ($response['success'] === false) {
            return $this->respondError($response['message'], $response['status']);
        }

        $responseData = $response['data'];

        return $this->respondWithData([
            'authorization' => $responseData['authorization'],
            'user' => UserResource::make($responseData['user'] ?? jwtGuard()->user()),
        ], $response['message'], $response['status']);
    }

    public function verifyOtp(Request $request, string $id, string $otp)
    {
        $response = $this->userRepo->verifyOtp($id, $otp, $request->boolean('remember'));
        $method = $response['success'] ? 'respondSuccess' : 'respondError';

        return $this->{$method}($response['message']);
    }

    /**
     * Get the authenticated User.
     */
    public function me(): JsonResponse
    {
        return $this->respondWithData(UserResource::make(jwtGuard()->user()));
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
            return $this->respondError('Failed to logout, please try again.', 500);
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
        $result = $this->userRepo->forgotPassword($validated);
        $method = $result['success']
            ? 'respondSuccess'
            : 'respondError';

        return $this->{$method}($result['message'], $result['status']);
    }

    public function resetPassword(ResetPasswordRequest $request, int | string $token)
    {
        $validated = $request->validated();

        try {
            $result = $this->userRepo->resetPassword(
                $validated['email'],
                $validated['password'],
                $validated['token']
            );
        } catch (Exception $e) {
            return $this->respondError("Failed to reset password, with error(s): {$e->getMessage()}");
        }

        $method = $result['success'] ? 'respondSuccess' : 'respondError';

        return $this->{$method}($result['message'], $result['status']);
    }
}
