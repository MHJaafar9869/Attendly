<?php

namespace Modules\Core\Repositories\User;

use App\Repositories\BaseRepository\BaseRepository;
use App\Traits\ResponseArray;
use App\Traits\SendEmail;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Modules\Core\Models\User;
use Modules\Core\Notifications\SendOtp;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    use ResponseArray, SendEmail;

    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    public function findByEmail(string $email)
    {
        return $this->addQuery()->where('email', $email)->first();
    }

    public function register(array $request): mixed
    {
        $request['password'] = Hash::make($request['password']);
        $request['role_id'] ??= 4;
        $request['status_id'] = 1;
        $user = $this->create($request);

        $otp = $this->generateOtp();
        $user->otp = $otp;
        $user->otp_expires_at = now()->addMinutes(10);
        $user->save();

        $user->notify(new SendOtp($otp, $user->last_name));

        return $this->arrayResponseSuccess(message: 'otp sent successfully', data: $user);
    }

    public function verifyOtp(string|int $userId, string $otp, ?bool $remember = false): array
    {
        $user = $this->find($userId);

        if (! $user) {
            return $this->arrayResponseError("User with id: {$userId} not found");
        }

        if ((bool) $user->is_verified) {
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
        $user->is_verified = true;
        $user->status_id = 2;
        $user->save();

        if ($remember) {
            $ttl = 60 * 24 * 30;
            jwtGuard()->factory()->setTTL($ttl);
        }

        return $this->arrayResponseSuccess('Otp verified successfully');
    }

    public function resetPassword(User $user, string $password, string $oldPassword, string $token): array
    {
        if (! empty($oldPassword) && ! Hash::check($oldPassword, $user->password)) {
            return $this->arrayResponseError('Old password is incorrect.');
        }

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
            return $this->arrayResponseError('Invalid or expired reset token.');
        }

        return $this->arrayResponseSuccess('Password updated successfully.');
    }
}
