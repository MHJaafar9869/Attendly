<?php

declare(strict_types=1);

namespace Modules\Core\DTO\Auth;

class ResetPasswordDto
{
    public function __construct(
        public string $email,
        public string $password,
        public string $token,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            email: $data['email'],
            password: $data['password'],
            token: $data['token'],
        );
    }

    public function toArray(): array
    {
        return [
            'email' => $this->email,
            'password' => $this->password,
            'token' => $this->token,
        ];
    }
}
