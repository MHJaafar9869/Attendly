<?php

namespace Modules\Core\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Traits\OTP;
use App\Traits\ResponseJson;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Core\Http\Requests\Auth\ForgotPasswordRequest;
use Modules\Core\Http\Requests\Auth\LoginRequest;
use Modules\Core\Http\Requests\Auth\RegisterRequest;
use Modules\Core\Http\Requests\Auth\ResetPasswordRequest;
use Modules\Core\Models\User;
use Modules\Core\Repositories\User\UserRepositoryInterface;
use Modules\Core\Transformers\User\UserResource;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
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
        $this->middleware('auth-user', ['except' => ['login', 'register', 'verifyOtp', 'forgotPassword']]);
    }

    /**
     * Get a JWT via given credentials.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->validated();
        $remember = $request->boolean('remember');
        $user = $this->userRepo->findByEmail($credentials['email']);

        try {
            if (! $user->email_verified_at) {
                return $this->respondError('Email is not verified');
            }

            if ($remember) {
                jwtGuard()->factory()->setTTL(60 * 24 * 30); // 30 days
            }

            if (! $token = jwtGuard()->attempt($credentials)) {
                return $this->respondError('Invalid credentials', 401);
            }

            $user = jwtGuard()->user()->load(['roles.permissions', 'status']);
            $user->last_visited_at = now();
            $user->save();

            return $this->respondWithToken($token, $user, 'Login Successful');
        } catch (JWTException $e) {
            return $this->respondError('Could not create token', 500);
        }
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $response = $this->userRepo->register($request->validated());

        return $this->respondWithData([
            'user' => $response['data']['user'],
            'authorization' => $response['data']['authorization'],
        ], $response['message']);
    }

    public function verifyOtp(Request $request, string $otp, string $id)
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

        return $this->{$method}($result['message']);
    }

    public function resetPassword(ResetPasswordRequest $request, int | string $id)
    {
        $validated = $request->validated();

        try {
            $result = $this->userRepo->resetPassword(
                jwtGuard()->user(),
                $validated['password'],
                $validated['old_password'],
                $validated['token']
            );
        } catch (Exception $e) {
            return $this->respondError("Failed to reset password, with error(s): {$e->getMessage()}");
        }

        $method = $result['success'] ? 'respondSuccess' : 'respondError';

        return $this->{$method}($result['message'], $result['status']);
    }

    /**
     * Get the token array structure.
     */
    protected function respondWithToken(string $token, ?User $user = null, ?string $message = null): JsonResponse
    {
        $data = [
            'authorization' => [
                'token_type' => 'bearer',
                'expires_in_sec' => jwtGuard()->factory()->getTTL() * 60,
                'token' => $token,
            ],
            'user' => new UserResource($user ?? jwtGuard()->user()),
        ];

        if ($message) {
            return $this->respondWithData($data, $message);
        }

        return $this->respondWithData($data);
    }
}
