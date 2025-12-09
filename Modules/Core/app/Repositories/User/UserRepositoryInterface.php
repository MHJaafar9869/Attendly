<?php

namespace Modules\Core\Repositories\User;

use App\Repositories\BaseRepository\BaseRepositoryInterface;
use Modules\Core\DTO\Auth\RegisterUserDto;
use Modules\Core\DTO\ImageDto\ImageUploadData;
use Modules\Core\DTO\ResponseDto\RepositoryResponseDto;
use Modules\Core\Models\User;

interface UserRepositoryInterface extends BaseRepositoryInterface
{
    public function login(User $user): RepositoryResponseDto;

    public function register(RegisterUserDto $dto): RepositoryResponseDto;

    public function verifyOtp(User $user, string $statusId): RepositoryResponseDto;

    public function enable2FA(User $user): RepositoryResponseDto;

    public function disable2FA(User $user): void;

    public function setup2FA(User $user): RepositoryResponseDto;

    public function confirm2FA(User $user, int $code): RepositoryResponseDto;

    public function uploadUserImage(ImageUploadData $data): RepositoryResponseDto;
}
