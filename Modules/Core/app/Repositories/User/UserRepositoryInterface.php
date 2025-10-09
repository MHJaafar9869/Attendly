<?php

namespace Modules\Core\Repositories\User;

use App\Repositories\BaseRepository\BaseRepositoryInterface;
use Modules\Core\Models\User;

interface UserRepositoryInterface extends BaseRepositoryInterface
{
    public function findByEmail(string $email);

    public function register(array $request);

    public function verifyOtp(string|int $userId, string $otp, ?bool $remember = false);

    public function forgotPassword(array $credentials);

    public function resetPassword(User $user, string $password, string $oldPassword, string $token);
}
