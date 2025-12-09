<?php

declare(strict_types=1);

namespace Modules\Core\DTO\Auth;

use Illuminate\Http\UploadedFile;

class UserImageDto
{
    public function __construct(
        public UploadedFile $file,
        public string $type,
        public string $userId,
    ) {}

    public static function fromRequest(array $data, string $userId): self
    {
        return new self(
            file: $data['image'],
            type: $data['type'] ?? 'profile',
            userId: $userId,
        );
    }
}
