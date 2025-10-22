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
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
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
        'first_name',
        'last_name',
        'slug_name',
        'email',
        'phone',
        'password',
        'address',
        'city',
        'country',
        'status_id',
        'device',
        'last_visited_at',
        'email_verified_at',
        'national_id',
    ];

    /**
     * The attributes that are hidden from resources.
     */
    protected $hidden = [
        'password',
        'remember_token',
        'otp',
        'otp_expires_at',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    protected $with = ['roles:id,name'];

    protected function casts(): array
    {
        return [
            'otp_expires_at' => 'datetime',
            'email_verified_at' => 'datetime',
            'last_visited_at' => 'datetime',
            'password' => 'hashed',
            'national_id' => 'hashed',
            'two_factor_recovery_codes' => 'array',
            'address' => 'encrypted',
            'city' => 'encrypted',
            'country' => 'encrypted',
        ];
    }

    // protected static function newFactory(): UserFactory
    // {
    //     // return UserFactory::new();
    // }

    /*
    |--------------------------------------------------------------------------
    | Tracking
    |--------------------------------------------------------------------------
    */
    public function trackables(): array
    {
        return [];
    }

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
        return $this->belongsToMany(Role::class, 'user_roles')->withTimestamps();
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function logs()
    {
        return $this->morphMany(ActivityLog::class, 'loggable', 'loggable_type', 'loggable_id');
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

    /*
    |--------------------------------------------------------------------------
    | Two Factor Authentication - Recovery Codes
    |--------------------------------------------------------------------------
    */

    public function generateRecoveryCodes(int $amount = 8): array
    {
        $codes = [];

        for ($idx = 0; $idx <= $amount; $idx++) {
            $codes[] = Str::random(10);
        }

        $hashed = collect($codes)->map(fn ($code) => Hash::make($code))->toArray();

        return [
            'plain' => $codes,
            'hashed' => $hashed,
        ];
    }

    public function verifyRecoveryCode(string $code): bool
    {
        foreach ($this->two_factor_recovery_codes as $idx => $hash) {
            if (Hash::check($code, $hash)) { // Authenticate code
                // Remove used code
                $codes = $this->two_factor_recovery_codes;
                unset($codes[$idx]);
                $this->two_factor_recovery_codes = array_values($codes);
                $this->save();

                return true;
            }
        }

        return false; // Failed authentication
    }
}
