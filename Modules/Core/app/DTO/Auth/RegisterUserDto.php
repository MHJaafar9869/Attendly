<?php

declare(strict_types=1);

namespace Modules\Core\DTO\Auth;

use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class RegisterUserDto
{
    public function __construct(
        public readonly string $firstName,
        public readonly string $lastName,
        public readonly string $slugName,
        public readonly string $email,
        public readonly string $password,
        public readonly string $otp,
        public readonly Carbon $otp_expires_at,
        public readonly int $roleId,
        public readonly int $statusId
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            firstName: $data['first_name'],
            lastName: $data['last_name'],
            slugName: strtolower(Str::slug("{$data['first_name']} {$data['last_name']}-" . Str::random(8))),
            email: $data['email'],
            password: $data['password'],
            otp: generateOtp(),
            otp_expires_at: now()->addMinutes((int) config('security.otp.otp_ttl_minutes')),
            roleId: $data['role_id'],
            statusId: $data['status_id'],
        );
    }

    public function toArray(): array
    {
        return [
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'slug_name' => $this->slugName,
            'email' => $this->email,
            'password' => $this->password,
            'otp' => $this->otp,
            'otp_expires_at' => $this->otp_expires_at,
            'status_id' => $this->statusId,
        ];
    }
}
