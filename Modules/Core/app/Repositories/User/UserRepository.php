<?php

namespace Modules\Core\Repositories\User;

use App\Repositories\BaseRepository\BaseRepository;
use Exception;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Modules\Core\DTO\RepositoryResponseDto;
use Modules\Core\DTO\User\ImageUploadData;
use Modules\Core\Enums\Status\StatusIDEnum;
use Modules\Core\Models\Image;
use Modules\Core\Models\User;
use Modules\Core\Notifications\EmailVerified;
use Modules\Core\Notifications\SendOtp;
use Modules\Core\Repositories\Role\RoleRepositoryInterface;
use Modules\Core\Repositories\Status\StatusRepositoryInterface;
use Modules\Core\Traits\HasImages;
use Modules\Core\Traits\ResponseArray;
use OTPHP\TOTP;

final readonly class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    use HasImages;
    use ResponseArray;

    public function __construct(
        User $model,
        protected RoleRepositoryInterface $roleRepo,
        protected StatusRepositoryInterface $statusRepo
    ) {
        parent::__construct($model);
    }

    public function login(array $data): RepositoryResponseDto
    {
        /** @var User $user */
        $user = $this->findBy('email', $data['email'], true);
        $remember = $data['remember'] ?? false;

        if (! $user instanceof Model) {
            return RepositoryResponseDto::error('User not found', 404);
        }

        if (! $user->email_verified_at) {
            return RepositoryResponseDto::error('Email is not verified');
        }

        if ($remember) {
            $ttl = 60 * 24 * (int) config('security.jwt.jwt_remember_ttl_days');
            jwtGuard()->factory()->setTTL($ttl);
        }

        $credentials = [
            'email' => $data['email'],
            'password' => $data['password'],
        ];

        if (! $token = jwtGuard()->attempt($credentials)) {
            return RepositoryResponseDto::error('Invalid credentials', 401);
        }

        $user = jwtGuard()->user()->load(['roles.permissions', 'status']);
        $user->touch('last_visited_at');
        $user->save();

        return RepositoryResponseDto::success()
            ->withMessage('Login successful')
            ->withData([
                'authorization' => [
                    'type' => 'bearer',
                    'expires_in_sec' => jwtGuard()->factory()->getTTL() * 60,
                    'token' => $token,
                ],
                'user' => $user,
            ]);
    }

    public function register(array $data): RepositoryResponseDto
    {
        return DB::transaction(function () use ($data): RepositoryResponseDto {
            if (! $statusId = $this->statusRepo->find(StatusIDEnum::USER_PENDING)->value('id')) {
                throw new Exception('Status not found');
            }
            $data['status_id'] = $statusId;
            $data['email'] = sanitize($data['email'], false);
            $data['slug_name'] = Str::slug("{$data['first_name']}-{$data['last_name']}-" . Str::random(8));
            $otp = generateOtp();
            $data['otp'] = $otp;
            $data['otp_expires_at'] = now()->addMinutes((int) config('security.otp.otp_ttl_minutes'));
            $user = $this->create($data);
            $role = $data['role'] ?? 'student';

            if (! $roleId = $this->roleRepo->findBy('name', $role)->value('id')) {
                throw new Exception('Role not found');
            }
            $user->roles()->syncWithoutDetaching([$roleId]);

            $token = jwtGuard()->login($user);

            DB::afterCommit(fn () => $user->notify(new SendOtp($otp)));

            return RepositoryResponseDto::success()
                ->withStatus(201)
                ->withMessage('OTP sent successfully')
                ->withData([
                    'authorization' => [
                        'type' => 'bearer',
                        'expires_in_sec' => jwtGuard()->factory()->getTTL() * 60,
                        'token' => $token,
                    ],
                    'user' => $user->load(['roles', 'status']),
                ]);
        });
    }

    public function verifyOtp(string | int $userId, string $otp, ?bool $remember = false): RepositoryResponseDto
    {
        return DB::transaction(function () use ($userId, $otp, $remember): RepositoryResponseDto {
            /** @var User $user */
            $user = $this->find($userId);

            if (! $user) {
                return RepositoryResponseDto::error("User with id: {$userId} not found");
            }

            if ($user->email_verified_at !== null) {
                return RepositoryResponseDto::success('User already verified');
            }

            if (! $user->otp || ! $user->otp_expires_at || $user->otp_expires_at->lt(now())) {
                return RepositoryResponseDto::error('OTP expired or invalid');
            }

            if (! Hash::check($otp, $user->otp)) {
                return RepositoryResponseDto::error('Invalid otp please try again');
            }

            $user->otp_expires_at = null;
            $user->otp = null;
            $user->touch('email_verified_at');
            if (! $statusId = $this->statusRepo->find(StatusIDEnum::USER_ACTIVE)->value('id')) {
                throw new Exception('error updating status');
            }
            $user->status_id = $statusId;
            $user->save();

            DB::afterCommit(fn () => $user->notify(new EmailVerified));

            if ($remember) {
                $ttl = 60 * 24 * (int) config('security.jwt.jwt_remember_ttl_days');
                jwtGuard()->factory()->setTTL($ttl);
            }

            return RepositoryResponseDto::success('Otp verified successfully');
        });
    }

    public function forgotPassword(array $credentials): RepositoryResponseDto
    {
        $user = $this->findBy('email', $credentials['email'], true);

        if (! $user instanceof Model) {
            return RepositoryResponseDto::error("User with email: {$credentials['email']}, not found");
        }

        $status = Password::sendResetLink($credentials);

        $method = $status === Password::ResetLinkSent
            ? 'success'
            : 'error';

        return RepositoryResponseDto::{$method}(__($status));
    }

    public function resetPassword(string $email, string $password, string $token): RepositoryResponseDto // FIXME
    {
        $status = Password::reset(
            ['email' => $email, 'password' => $password, 'password_confirmation' => $password, 'token' => $token],
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->setRememberToken(Str::random(60));
                $user->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::INVALID_TOKEN) {
            return RepositoryResponseDto::error('Invalid or expired reset token.', 422);
        }

        return RepositoryResponseDto::success('Password updated successfully.');
    }

    public function enable2FA(User $user): RepositoryResponseDto
    {
        if ($user->two_factor_secret) {
            return RepositoryResponseDto::success('2FA is already activated');
        }

        $otp = TOTP::generate();
        $recoveryCodes = $user->generateRecoveryCodes();

        $user->forceFill([
            'two_factor_secret' => $otp->getSecret(),
            'two_factor_recovery_codes' => $recoveryCodes['hashed'],
        ])->save();

        return RepositoryResponseDto::success()
            ->withMessage('2FA has been enabled successfully')
            ->withData(['recovery_codes' => $recoveryCodes['plain']]);
    }

    public function disable2FA(User $user): void
    {
        $user->forceFill([
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
        ])->save();
    }

    public function setup2FA(User $user): RepositoryResponseDto
    {
        $codes = $user->generateRecoveryCodes();

        $secret = $user->two_factor_secret;
        $otp = TOTP::createFromSecret($secret);
        $otp->setLabel($user->email);
        $otp->setIssuer(config('app.name'));

        $uri = $otp->getProvisioningUri();

        return RepositoryResponseDto::success()
            ->withMessage('2FA has been created successfully')
            ->withData([
                'recovery_codes' => $codes['plain'],
                'secret' => $secret,
                'uri' => $uri,
            ]);
    }

    public function confirm2FA(User $user, int $code): RepositoryResponseDto
    {
        $secret = $user->two_factor_secret;

        if (! $secret) {
            return RepositoryResponseDto::error('2FA not enabled', 400);
        }

        $otp = TOTP::createFromSecret($secret);
        $code = trim($code);

        if (ctype_digit($code) && strlen($code) === 6) {
            if (! $otp->verify($code)) {
                return RepositoryResponseDto::error('Invalid code');
            }

            $token = jwtGuard()->claims(['amr' => ['mfa']])->fromUser($user);

            return RepositoryResponseDto::success()
                ->withMessage('Logged in using 2FA')
                ->withData($token);
        }

        if ($user->verifyRecoveryCode($code)) {
            $token = jwtGuard()->claims(['amr' => ['mfa']])->fromUser($user);

            return RepositoryResponseDto::success()
                ->withMessage('Logged in using recovery codes')
                ->withData($token);
        }

        return RepositoryResponseDto::error('Invalid 2FA code');
    }

    public function uploadProfilePicture(ImageUploadData $data): RepositoryResponseDto
    {
        /** @var User $user */
        $user = $this->find($data->userId);

        if (! $user) {
            return RepositoryResponseDto::error('User not found', 404);
        }

        return DB::transaction(function () use ($user, $data) {
            $type = $data->type !== '' && $data->type !== '0';
            if ($type) {
                $profileImage = $user->images()->where('type', $data->type)->first();

                if ($profileImage) {
                    $this->deleteFile($profileImage->image_path, $data->disk);
                    $profileImage->delete();
                }
            }

            $image = $user->images()->save(Image::fromDTO($data));

            $message = $type
                ? normalize('_', ' ', $data->type) . ' uploaded successfully'
                : 'Profile picture uploaded successfully';

            return RepositoryResponseDto::success(
                message: $message,
                data: ['image' => $image],
                statusCode: 201
            );
        });
    }
}
