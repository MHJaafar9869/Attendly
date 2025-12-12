<?php

namespace Modules\Core\Services\UserServices;

use Exception;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Modules\Core\DTO\Auth\LoginUserDto;
use Modules\Core\DTO\Auth\RegisterUserDto;
use Modules\Core\DTO\Auth\ResetPasswordDto;
use Modules\Core\DTO\Auth\UserImageDto;
use Modules\Core\DTO\Auth\VerifyOTPDto;
use Modules\Core\DTO\ImageDto\ImageUploadData;
use Modules\Core\DTO\ResponseDto\ServiceResponseDto;
use Modules\Core\Models\User;
use Modules\Core\Repositories\Role\RoleRepositoryInterface;
use Modules\Core\Repositories\Status\StatusRepositoryInterface;
use Modules\Core\Repositories\User\UserRepositoryInterface;
use Modules\Core\Services\BaseService;
use Modules\Core\Traits\UploadFile;

final readonly class AuthService extends BaseService
{
    use UploadFile;

    public function __construct(
        protected UserRepositoryInterface $userRepo,
        protected RoleRepositoryInterface $roleRepo,
        protected StatusRepositoryInterface $statusRepo
    ) {}

    public function login(LoginUserDto $loginUserDto): ServiceResponseDto
    {
        try {
            /** @var User $user */
            $user = $this->userRepo->findBy([
                'email' => $loginUserDto->email,
            ]);

            if (! $user) {
                return ServiceResponseDto::error('Invalid credentials', 401);
            }

            if (! Hash::check($loginUserDto->password, $user->password)) {
                return ServiceResponseDto::error('Invalid credentials', 401);
            }

            $response = $this->userRepo->login($user);

        } catch (Exception $e) {
            return $this->getErrorResponse($e, 500, 'Login failed. Please try again');
        }

        return ServiceResponseDto::response($response);
    }

    public function register(RegisterUserDto $registerUserDto): ServiceResponseDto
    {
        try {
            $response = $this->userRepo->register($registerUserDto);
        } catch (Exception $e) {
            return $this->getErrorResponse($e, 500, 'Registration failed. Please try again later');
        }

        return ServiceResponseDto::response($response);
    }

    public function verifyOtp(VerifyOTPDto $dto): ServiceResponseDto
    {
        try {
            /** @var User $user */
            if (! ($user = $this->userRepo->findBy(['slug_name' => $dto->userSlug]))) {
                return ServiceResponseDto::error('Invalid Access, please login again', 401);
            }

            if ($user->email_verified_at !== null) {
                return ServiceResponseDto::success('User already verified');
            }

            if (! $user->otp || ! $user->otp_expires_at || $user->otp_expires_at->lt(now())) {
                return ServiceResponseDto::error('OTP expired or invalid');
            }

            if (! Hash::check($dto->otp, $user->otp)) {
                return ServiceResponseDto::error('Invalid otp please try again');
            }

            $response = $this->userRepo->verifyOtp($user, $dto->statusId);

        } catch (Exception $e) {
            return $this->getErrorResponse($e, 500, 'OTP verification failed. Please try again later');
        }

        return ServiceResponseDto::response($response);
    }

    public function forgotPassword(array $credentials): ServiceResponseDto
    {
        $user = $this->userRepo->findBy(['email' => $credentials['email']]);

        if (! $user instanceof Model) {
            return ServiceResponseDto::error('Invalid credentials', 401);
        }

        $status = Password::sendResetLink($credentials);

        $method = $status === Password::ResetLinkSent
            ? 'success'
            : 'error';

        return ServiceResponseDto::{$method}(__($status));
    }

    public function resetPassword(ResetPasswordDto $dto): ServiceResponseDto
    {
        try {
            $status = Password::reset(
                $dto->toArray(),
                function ($user, $password) {
                    $user->forceFill([
                        'password' => Hash::make($password),
                    ])->setRememberToken(Str::random(60));
                    $user->save();

                    event(new PasswordReset($user));
                }
            );

            if ($status === Password::INVALID_TOKEN) {
                return ServiceResponseDto::error('Invalid or expired reset token', 422);
            }
        } catch (Exception $e) {
            return $this->getErrorResponse($e, 500);
        }

        return ServiceResponseDto::success('Password updated successfully');
    }

    public function uploadUserImage(UserImageDto $dto): ServiceResponseDto
    {
        try {
            if (! $user = sanctumUser()) {
                return ServiceResponseDto::error('Invalid Access', 400);
            }

            $role = match (true) {
                $user->hasRole('teacher') => 'teacher',
                $user->hasRole('student') => 'student',
                default => 'user'
            };

            $path = $this->uploadFile($dto->file, "$role/{$user->slugName}/{$dto->type}");

            $trimSlugSuffix = preg_replace('/-[A-Za-z0-9]{8}$/', '', $user->slugName);
            $alt = normalize('-', ' ', $trimSlugSuffix).' '.normalize('_', ' ', $dto->type);

            $imageDto = ImageUploadData::make(
                path: $path,
                type: $dto->type,
                disk: 'public',
                url: $this->fileUrl($path),
                mime: $this->getMime($dto->file),
                alt: $alt,
            );

            $response = $this->userRepo->uploadUserImage($imageDto);
        } catch (Exception $e) {
            return $this->getErrorResponse($e, 500);
        }

        return ServiceResponseDto::response($response);
    }
}
