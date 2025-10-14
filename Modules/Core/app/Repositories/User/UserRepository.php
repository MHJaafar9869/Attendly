<?php

namespace Modules\Core\Repositories\User;

use App\Repositories\BaseRepository\BaseRepository;
use App\Traits\OTP;
use App\Traits\ResponseArray;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Modules\Core\Models\User;
use Modules\Core\Notifications\EmailVerified;
use Modules\Core\Notifications\SendOtp;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    use OTP;
    use ResponseArray;

    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    public function findByEmail(string $email)
    {
        return $this->addQuery()->where('email', $email)->first();
    }

    public function login(array $data)
    {
        $user = $this->findByEmail($data['email']);
        $remember = $data['remember'] ?? false;

        if (! $user) {
            return $this->arrayResponseError('User not found', 404);
        }

        if (! $user->email_verified_at) {
            return $this->arrayResponseError('Email is not verified');
        }

        if (! $token = jwtGuard()->attempt($data)) {
            return $this->arrayResponseError('Invalid credentials', 401);
        }

        if ($remember) {
            jwtGuard()->factory()->setTTL(60 * 24 * 30); // 30 days
        }

        $user = jwtGuard()->user()->load(['roles.permissions', 'status']);
        $user->last_visited_at = now();
        $user->save();

        return $this->arrayResponseSuccess('Login Successful', data: [
            'user' => $user,
            'authorization' => [
                'type' => 'bearer',
                'expires_in_sec' => jwtGuard()->factory()->getTTL() * 60,
                'token' => $token,
            ],
        ]);
    }

    public function register(array $data): mixed
    {
        return DB::transaction(function () use ($data) {
            $data['status_id'] = 1;
            $data['slug_name'] = Str::slug($data['first_name'].' '.$data['last_name']).'-'.uniqid();
            $user = $this->create($data);

            $otp = $this->generateOtp();
            $user->otp = $otp;
            $user->otp_expires_at = now()->addMinutes(10);
            $user->save();

            $token = jwtGuard()->login($user);

            DB::afterCommit(fn () => $user->notify(new SendOtp($otp)));

            return $this->arrayResponseSuccess(message: 'otp sent successfully', data: [
                'user' => $user->load(['roles.permissions', 'status']),
                'authorization' => [
                    'type' => 'bearer',
                    'expires_in_sec' => jwtGuard()->factory()->getTTL() * 60,
                    'token' => $token,
                ],
            ]);
        });
    }

    public function verifyOtp(string|int $userId, string $otp, ?bool $remember = false): array
    {
        return DB::transaction(function () use ($userId, $otp, $remember) {
            $user = $this->find($userId);

            if (! $user) {
                return $this->arrayResponseError("User with id: {$userId} not found");
            }

            if ($user->email_verified_at !== null) {
                return $this->arrayResponseSuccess('User already verified');
            }

            if (! $user->otp || ! $user->otp_expires_at || $user->otp_expires_at->lt(now())) {
                return $this->arrayResponseError('OTP expired or invalid');
            }

            if ((string) $otp !== (string) $user->otp) {
                return $this->arrayResponseError('Invalid otp please try again');
            }

            $user->otp_expires_at = null;
            $user->otp = null;
            $user->email_verified_at = now();
            $user->status_id = 2;
            $user->save();

            DB::afterCommit(fn () => $user->notify(new EmailVerified));

            if ($remember) {
                $ttl = 60 * 24 * 30;
                jwtGuard()->factory()->setTTL($ttl);
            }

            return $this->arrayResponseSuccess('Otp verified successfully');
        });
    }

    public function forgotPassword(array $credentials)
    {
        $user = $this->findByEmail($credentials['email']);

        if (! $user) {
            return $this->arrayResponseError("User with email: {$credentials['email']}, not found");
        }

        $status = Password::sendResetLink($credentials);

        $result = $status === Password::ResetLinkSent
            ? ['method' => 'arrayResponseSuccess', 'message' => 'Reset password email has been sent successfully']
            : ['method' => 'arrayResponseError', 'message' => 'Failed to send reset password email'];

        return $this->{$result['method']}($result['message']);
    }

    public function resetPassword(User $user, string $password, string $oldPassword, string $token): array
    {
        $status = Password::reset(
            ['email' => $user->email, 'password' => $password, 'password_confirmation' => $password, 'token' => $token],
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->setRememberToken(Str::random(60));
                $user->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::INVALID_TOKEN) {
            return $this->arrayResponseError('Invalid or expired reset token.', 422);
        }

        return $this->arrayResponseSuccess('Password updated successfully.');
    }
}
