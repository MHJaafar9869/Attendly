<?php

namespace Modules\Core\Repositories\User;

use App\Repositories\BaseRepository\BaseRepository;
use Illuminate\Support\Facades\DB;
use Modules\Core\DTO\Auth\RegisterUserDto;
use Modules\Core\DTO\ImageDto\ImageUploadData;
use Modules\Core\DTO\ResponseDto\RepositoryResponseDto;
use Modules\Core\Models\User;
use Modules\Core\Notifications\EmailVerified;
use Modules\Core\Notifications\SendOtp;
use Modules\Core\Repositories\Role\RoleRepositoryInterface;
use Modules\Core\Repositories\Status\StatusRepositoryInterface;
use Modules\Core\Traits\HasImages;
use Modules\Core\Traits\ResponseArray;
use Modules\Core\Transformers\User\UserResource;
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

    public function login(User $user): RepositoryResponseDto
    {
        $token = $user->createToken(
            'auth_token',
            expiresAt: now()->addDays((int) config('security.sanctum.token_expiry_days', 30))
        )->plainTextToken;

        $user->is_logged_in = true;
        $user->touch('last_visited_at');
        $user->save();

        return RepositoryResponseDto::success()
            ->setMessage('Login successful')
            ->setToken($token)
            ->setData([
                'authorization' => [
                    'type' => 'bearer',
                    'token' => $token,
                ],
                'user' => UserResource::make(
                    $user->load([
                        'roles:id,name',
                        'roles.permissions:id,name',
                        'status:id,name,text_color,bg_color',
                    ])
                ),
            ]);
    }

    public function register(RegisterUserDto $dto): RepositoryResponseDto
    {
        return DB::transaction(function () use ($dto): RepositoryResponseDto {
            /** @var User $user */
            $user = $this->create($dto->toArray());

            $user->roles()->syncWithoutDetaching([$dto->roleId]);

            DB::afterCommit(function () use ($user, $dto) {
                $user->notify(new SendOtp($dto->otp));
            });

            $token = $user->createToken(
                'auth_token',
                expiresAt: now()->addDays((int) config('security.sanctum.token_expiry_days', 30))
            )->plainTextToken;

            return RepositoryResponseDto::success()
                ->setStatus(201)
                ->setMessage('OTP sent successfully')
                ->setData([
                    'authorization' => [
                        'type' => 'bearer',
                        'token' => $token,
                    ],
                    'user' => UserResource::make(
                        $user->load([
                            'roles:id,name',
                            'roles.permissions:id,name',
                            'status:id,name,text_color,bg_color',
                        ])
                    ),
                ]);
        });
    }

    public function verifyOtp(User $user, string $statusId): RepositoryResponseDto
    {
        $user->update([
            'otp' => null,
            'otp_expires_at' => null,
            'email_verified_at' => now(),
            'status_id' => $statusId,
        ]);

        $user->notify(new EmailVerified);

        return RepositoryResponseDto::success('Otp verified successfully');
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
            ->setMessage('2FA has been enabled successfully')
            ->setData(['recovery_codes' => $recoveryCodes['plain']]);
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
            ->setMessage('2FA has been created successfully')
            ->setData([
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

        if (ctype_digit($code) && \strlen($code) === 6) {
            if (! $otp->verify($code)) {
                return RepositoryResponseDto::error('Invalid code');
            }

            $token = $user->createToken('2fa-auth')->plainTextToken;

            return RepositoryResponseDto::success()
                ->setMessage('Logged in using 2FA')
                ->setData([
                    'authorization' => [
                        'type' => 'bearer',
                        'token' => $token,
                    ],
                    'user' => $user,
                ]);
        }

        if ($user->verifyRecoveryCode($code)) {

            // Sanctum token after recovery code
            $token = $user->createToken('2fa-auth')->plainTextToken;

            return RepositoryResponseDto::success()
                ->setMessage('Logged in using recovery codes')
                ->setData([
                    'authorization' => [
                        'type' => 'bearer',
                        'token' => $token,
                    ],
                    'user' => $user,
                ]);
        }

        return RepositoryResponseDto::error('Invalid 2FA code');
    }

    public function uploadUserImage(ImageUploadData $dto): RepositoryResponseDto
    {
        return DB::transaction(function () use ($dto) {
            $user = sanctumUser();
            $type = $dto->type;
            $profileImage = $user->images()->where('type', $type)->first();

            if ($profileImage) {
                $existingPath = $profileImage->image_path;
                $profileImage->delete();

                DB::afterCommit(function () use ($existingPath, $dto) {
                    $this->deleteFile($existingPath, $dto->disk);
                });
            }

            $image = $user->images()->create($dto->toArray());

            $message = $type !== '' && $type !== '0'
                ? normalize('_', ' ', $type) . ' uploaded successfully'
                : 'Profile picture uploaded successfully';

            return RepositoryResponseDto::success()
                ->setMessage($message)
                ->setData(['image' => $image])
                ->setStatus(201);
        });
    }
}
