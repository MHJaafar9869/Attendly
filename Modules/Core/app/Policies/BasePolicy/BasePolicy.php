<?php

declare(strict_types=1);

namespace Modules\Core\Policies\BasePolicy;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Database\Eloquent\Model;
use Modules\Core\Models\User;

class BasePolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     */
    public function __construct() {}

    public function isAdmin(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function isSuperAdmin(User $user): bool
    {
        return $user->hasRole('super_admin');
    }

    public function isOwner(User $user, Model $rersouce): bool
    {
        return $user->id === $rersouce->user_id;
    }

    public function view(User $user, Model $resource): true
    {
        return $this->isOwner($user, $resource) || $this->isAdmin($user) || $this->isSuperAdmin($user);
    }

    public function update(User $user, Model $resource): bool
    {
        return $this->isOwner($user, $resource) || $this->isAdmin($user) || $this->isSuperAdmin($user);
    }
}
