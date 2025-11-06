<?php

namespace Modules\Core\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Modules\Core\Observers\LogObserver;
use Modules\Domain\Models\Student;
use Modules\Domain\Models\Teacher;
use Tymon\JWTAuth\Contracts\JWTSubject;

// use Modules\Core\Database\Factories\UserFactory;
#[ObservedBy(LogObserver::class)]
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
        'password',
        'status_id',
        'device',
        'last_visited_at',
        'email_verified_at',
    ];

    /**
     * The attributes that are hidden from resources.
     */
    protected $hidden = [
        'password',
        'remember_token',
        'token_version',
        'otp',
        'otp_expires_at',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    protected $with = ['roles:id,name'];

    protected $appends = ['fullname', 'status_name'];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'otp' => 'hashed',

            'two_factor_secret' => 'encrypted',
            'two_factor_recovery_codes' => 'encrypted:array',

            'otp_expires_at' => 'datetime',
            'email_verified_at' => 'datetime',
            'last_visited_at' => 'datetime',
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
        return ['all'];
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
        return $this->belongsToMany(Role::class, 'user_roles')
            ->withPivot('assigned_by')
            ->withTimestamps();
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function teachers()
    {
        return $this->hasMany(Teacher::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function contacts()
    {
        return $this->hasMany(Contact::class);
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
    | Scopes
    |--------------------------------------------------------------------------
    */

    #[Scope]
    public function byRole(Builder $query, array|string $roles): Builder
    {
        return $query->whereHas(
            'roles',
            fn ($q) => $q->whereIn('name', (array) $roles)
        );
    }

    #[Scope]
    public function byStatus(Builder $query, array|string $status): Builder
    {
        return $query->whereHas(
            'status',
            fn ($q) => $q->where('context', 'user')->whereIn('name', (array) $status)
        );
    }

    #[Scope]
    public function withStatus(Builder $query): Builder
    {
        return $query->with(['status' => fn ($q) => $q->where('context', 'user')]);
    }

    #[Scope]
    public function has2FA(Builder $query): Builder
    {
        return $query->whereNotNull('two_factor_secret');
    }

    #[Scope]
    public function withContacts(Builder $query, bool $active = true, ?int $typeId = null): Builder
    {
        return $query->whereHas('contacts', function ($q) use ($active, $typeId) {
            $q->where('is_active', $active);

            if ($typeId !== null) {
                $q->where('type_id', $typeId);
            }
        });
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

    /*
    |--------------------------------------------------------------------------
    | Attributes
    |--------------------------------------------------------------------------
    */

    public function fullname(): Attribute
    {
        return Attribute::make(get: fn () => "{$this->first_name} {$this->last_name}");
    }

    public function statusName(): Attribute
    {
        return Attribute::make(get: fn () => $this->status->name);
    }
}
