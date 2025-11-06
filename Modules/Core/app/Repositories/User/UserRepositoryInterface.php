<?php

namespace Modules\Core\Repositories\User;

use App\Repositories\BaseRepository\BaseRepositoryInterface;
use Modules\Core\DTO\RepositoryResponseDto;
use Modules\Core\DTO\User\ImageUploadData;
use Modules\Core\Models\User;

interface UserRepositoryInterface extends BaseRepositoryInterface
{
    public function login(array $data): RepositoryResponseDto;

    public function register(array $data): RepositoryResponseDto;

    public function verifyOtp(string | int $userId, string $otp, ?bool $remember = false): RepositoryResponseDto;

    public function forgotPassword(array $credentials): RepositoryResponseDto;

    public function resetPassword(string $email, string $password, string $token): RepositoryResponseDto;

    public function enable2FA(User $user): RepositoryResponseDto;

    public function disable2FA(User $user): void;

    public function setup2FA(User $user): RepositoryResponseDto;

    public function confirm2FA(User $user, int $code): RepositoryResponseDto;

    public function uploadProfilePicture(ImageUploadData $data): RepositoryResponseDto;
}
