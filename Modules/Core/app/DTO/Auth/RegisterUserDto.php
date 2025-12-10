<?php

declare(strict_types=1);

namespace Modules\Core\DTO\Auth;

use Carbon\Carbon;
use Illuminate\Support\Str;

class RegisterUserDto
{
    public function __construct(
        public string $firstName,
        public string $lastName,
        public readonly string $slugName,
        public string $email,
        public string $password,
        public readonly string $otp,
        public readonly Carbon $otp_expires_at,
        public ?int $roleId = null,
        public ?int $statusId = null
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            firstName: $data['first_name'],
            lastName: $data['last_name'],
            slugName: Str::slug("{$data['first_name']}-{$data['last_name']}-" . Str::random(8)),
            email: $data['email'],
            password: $data['password'],
            otp: generateOtp(),
            otp_expires_at: now()->addMinutes((int) config('security.otp.otp_ttl_minutes')),
            roleId: $data['role_id'],
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

    public function setStatusId(int $statusId): void
    {
        $this->statusId = $statusId;
    }

    public function setRoleId(int $roleId): void
    {
        $this->roleId = $roleId;
    }
}
