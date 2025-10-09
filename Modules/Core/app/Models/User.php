<?php

namespace Modules\Core\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

// use Modules\Core\Database\Factories\UserFactory;

class User extends Authenticatable implements FilamentUser, HasName, JWTSubject
{
    use HasFactory;
    use HasUlids;
    use Notifiable;
    use SoftDeletes;

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
    |  JWT & Filament
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

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->hasRole('super_admin');
    }

    public function getFilamentName(): string
    {
        return "{$this->first_name} {$this->last_name}";
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

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function userActions()
    {
        return $this->belongsToMany(Action::class, 'user_actions');
    }

    public function images()
    {
        return $this->morphMany(Image::class, 'imageable', 'imageable_type', 'imageable_id');
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

        return $rolePermissions->contains(strtolower($permission));
    }

    public function getRoles(): array
    {
        return $this->roles->pluck('name')->toArray();
    }

    public function getPermissions(): array
    {
        return $this->roles->flatMap->permissions->pluck('name')->toArray();
    }
}
