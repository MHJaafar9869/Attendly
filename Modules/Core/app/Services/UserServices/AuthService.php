<?php

namespace Modules\Core\Services\UserServices;

use Illuminate\Http\UploadedFile;
use Modules\Core\DTO\User\ImageUploadData;
use Modules\Core\Repositories\User\UserRepositoryInterface;
use Modules\Core\Traits\UploadFile;

class AuthService
{
    use UploadFile;

    public function __construct(
        protected UserRepositoryInterface $userRepo
    ) {
        // code...
    }

    public function uploadUserImage(
        UploadedFile $file,
        int | string $userId,
        string $slugName,
        string $type,
        string $disk = 'public'
    ) {
        $path = $this->uploadFile($file, "users/{$slugName}/{$type}");

        $namePart = preg_replace('/-[A-Za-z0-9]{8}$/', '', $slugName);
        $alt = normalize('-', ' ', $namePart) . ' ' . normalize('_', ' ', $type);

        $dto = new ImageUploadData(
            userId: $userId,
            path: $path,
            type: $type,
            disk: $disk,
            url: $this->fileUrl($path, $disk),
            mime: $this->getMime($file),
            alt: $alt,
        );

        return $this->userRepo->uploadProfilePicture($dto);
    }
}
