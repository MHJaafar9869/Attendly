<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

// use Modules\Core\Database\Factories\UserFactory;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, HasUlids, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'first_name', 'last_name', 'slug_name',
        'email', 'phone', 'password',
        'address', 'city', 'country',
        'status_id', 'device',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'otp',
        'otp_expires_at',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'otp_expires_at' => 'datetime',
            'last_visited_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected $with = ['roles'];

    // protected static function newFactory(): UserFactory
    // {
    //     // return UserFactory::new();
    // }

    /*
    |--------------------------------------------------------------------------
    |  JWT
    |--------------------------------------------------------------------------
    |
    */

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        $roles = $this->getRoles();
        $permissions = $this->getPermissions();

        return [
            'id' => $this->id,
            'name' => $this->name,
            'roles' => $roles,
            'permissions' => $permissions,
            'token_version' => $this->token_version,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    |  Relations
    |--------------------------------------------------------------------------
    |
    */

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'user_permissions');
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function actions()
    {
        return $this->belongsToMany(Action::class, 'user_actions');
    }

    /*
    |--------------------------------------------------------------------------
    | Role & Permission Helpers
    |--------------------------------------------------------------------------
    */

    public function hasRole(string $role): bool
    {
        return $this->roles->pluck('name')->map(fn ($r) => strtolower($r))->contains(strtolower($role));
    }

    public function hasAnyRole(array $roles): bool
    {
        $lowerRoles = array_map('strtolower', $roles);

        return $this->roles->pluck('name')->map(fn ($r) => strtolower($r))->intersect($lowerRoles)->isNotEmpty();
    }

    public function hasPermission(string $permission): bool
    {
        $rolePermissions = $this->roles->flatMap->permissions->pluck('name')->map(fn ($p) => strtolower($p));
        $userPermissions = $this->permissions->pluck('name')->map(fn ($p) => strtolower($p));

        return $rolePermissions->contains(strtolower($permission)) || $userPermissions->contains(strtolower($permission));
    }

    public function getRoles(): array
    {
        return $this->roles->pluck('name')->toArray();
    }

    public function getPermissions(): array
    {
        $userPermissions = $this->permissions->pluck('name')->toArray();
        $rolePermissions = $this->roles->flatMap->permissions->pluck('name')->toArray();

        return $rolePermissions->merge($userPermissions)->unique()->toArray();
    }
}
