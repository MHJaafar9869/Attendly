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
use Modules\Core\DTO\ImageDto\ImageUploadData;
use Modules\Core\DTO\ResponseDto\ServiceResponseDto;
use Modules\Core\Enums\Status\StatusIDEnum;
use Modules\Core\Models\User;
use Modules\Core\Repositories\Role\RoleRepositoryInterface;
use Modules\Core\Repositories\Status\StatusRepositoryInterface;
use Modules\Core\Repositories\User\UserRepositoryInterface;
use Modules\Core\Traits\UploadFile;
use Throwable;

class AuthService
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
            $user = $this->userRepo->findBy('email', $loginUserDto->email, true);

            if (! $user) {
                return ServiceResponseDto::error('Invalid credentials', 401);
            }

            if (! Hash::check($loginUserDto->password, $user->password)) {
                return ServiceResponseDto::error('Invalid credentials', 401);
            }

            if (! $user->email_verified_at) {
                return ServiceResponseDto::error('Email is not verified');
            }

            $response = $this->userRepo->login($user);

        } catch (Exception $e) {
            return app()->environment('local')
                ? ServiceResponseDto::error('Failed due: ' . $e->getMessage(), 500)
                : ServiceResponseDto::error('Login failed. Please try again', 500);
        }

        return ServiceResponseDto::response($response);
    }

    public function register(RegisterUserDto $registerUserDto): ServiceResponseDto
    {
        try {
            if (! $statusId = $this->statusRepo->findAndSelect(StatusIDEnum::USER_PENDING->value, 'id')->id) {
                return ServiceResponseDto::error('Status not found');
            }

            if (! $roleId = $this->roleRepo->findAndSelect($registerUserDto->roleId, 'id')->id) {
                return ServiceResponseDto::error('Role not found');
            }

            $registerUserDto->setRoleId($roleId);
            $registerUserDto->setStatusId($statusId);

            $response = $this->userRepo->register($registerUserDto);
        } catch (Throwable $th) {
            return app()->environment('local')
                ? ServiceResponseDto::error('Failed due: ' . $th->getMessage(), 500)
                : ServiceResponseDto::error('Registration failed. Please try again later.', 500);
        }

        return ServiceResponseDto::response($response);
    }

    public function verifyOtp(string $userSlug, string $otp): ServiceResponseDto
    {
        try {
            /** @var User $user */
            if (! ($user = $this->userRepo->findBy('slug_name', $userSlug, true)) instanceof Model) {
                return ServiceResponseDto::error('Invalid Access, please login again', 401);
            }

            if ($user->email_verified_at !== null) {
                return ServiceResponseDto::success('User already verified');
            }

            if (! $user->otp || ! $user->otp_expires_at || $user->otp_expires_at->lt(now())) {
                return ServiceResponseDto::error('OTP expired or invalid');
            }

            if (! Hash::check($otp, $user->otp)) {
                return ServiceResponseDto::error('Invalid otp please try again');
            }

            if (! $statusId = $this->statusRepo->find(StatusIDEnum::USER_ACTIVE->value)->value('id')) {
                return ServiceResponseDto::error('error updating status');
            }

            $response = $this->userRepo->verifyOtp($user, $statusId);
        } catch (Throwable $th) {
            return app()->environment('local')
                ? ServiceResponseDto::error('Failed due: ' . $th->getMessage(), 500)
                : ServiceResponseDto::error('OTP verification failed. Please try again later.', 500);
        }

        return ServiceResponseDto::response($response);
    }

    public function forgotPassword(array $credentials): ServiceResponseDto
    {
        $user = $this->userRepo->findBy('email', $credentials['email'], true);

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
        } catch (Throwable $th) {
            return ServiceResponseDto::error('Error occurred. Please try again', 500);
        }

        return ServiceResponseDto::success('Password updated successfully');
    }

    public function uploadUserImage(UserImageDto $dto): ServiceResponseDto
    {
        try {
            if (! ($user = sanctumUser()) instanceof User) {
                return ServiceResponseDto::error('Invalid Access', 400);
            }

            $path = $this->uploadFile($dto->file, "users/{$user->slugName}/{$dto->type}");

            $trimSlugSuffix = preg_replace('/-[A-Za-z0-9]{8}$/', '', $user->slugName);
            $alt = normalize('-', ' ', $trimSlugSuffix) . ' ' . normalize('_', ' ', $dto->type);

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
            return app()->environment('local')
                ? ServiceResponseDto::error('Failed due: ' . $e->getMessage(), 500)
                : ServiceResponseDto::error('Error occurred. Please try again', 500);
        }

        return ServiceResponseDto::response($response);
    }
}
