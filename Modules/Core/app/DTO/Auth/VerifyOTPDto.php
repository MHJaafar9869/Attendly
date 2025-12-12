<?php

declare(strict_types=1);

namespace Modules\Core\DTO\Auth;

class VerifyOTPDto
{
    public function __construct(
        public readonly string $otp,
        public readonly int $statusId,
        public readonly string $userSlug
    ) {}

    public static function fromRequest(array $data, $userSlug): self
    {
        return new self(
            otp: $data['otp'],
            statusId: $data['status_id'],
            userSlug: $userSlug
        );
    }
}
