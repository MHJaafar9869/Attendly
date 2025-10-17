<?php

namespace Modules\Core\Repositories\User;

use App\Repositories\BaseRepository\BaseRepositoryInterface;
use Modules\Core\Models\User;

interface UserRepositoryInterface extends BaseRepositoryInterface
{
    public function login(array $data);

    public function register(array $data);

    public function verifyOtp(string | int $userId, string $otp, ?bool $remember = false);

    public function forgotPassword(array $credentials);

    public function resetPassword(string $email, string $password, string $token);
}
