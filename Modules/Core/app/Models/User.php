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
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Modules\Core\Observers\LogObserver;
use Modules\Domain\Models\Student;
use Modules\Domain\Models\Teacher;

// use Modules\Core\Database\Factories\UserFactory;
/**
 * @property-read string $id
 * @property-read string $first_name
 * @property-read string $last_name
 * @property-read string|null $slug_name
 * @property-read string $email
 * @property-read string $password
 * @property-read string $gender
 * @property-read int|null $status_id
 * @property-read int $token_version
 * @property-read string|null $otp
 * @property-read Carbon|null $otp_expires_at
 * @property-read string|null $two_factor_secret
 * @property-read array|null $two_factor_recovery_codes
 * @property-read bool $is_logged_in
 * @property-read Carbon|null $last_visited_at
 * @property-read Carbon|null $email_verified_at
 * @property-read string|null $remember_token
 * @property-read Carbon $created_at
 * @property-read Carbon $updated_at
 * @property-read Carbon|null $deleted_at
 */
#[ObservedBy(LogObserver::class)]
class User extends Authenticatable implements FilamentUser, HasName
{
    use HasApiTokens;
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
        'gender',
        'status_id',
        'device',
        'last_visited_at',
        'email_verified_at',
        'is_logged_in',
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

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'otp' => 'hashed',

            'two_factor_secret' => 'encrypted',
            'two_factor_recovery_codes' => 'encrypted:array',

            'is_logged_in' => 'boolean',
            'otp_expires_at' => 'datetime',
            'email_verified_at' => 'datetime',
            'last_visited_at' => 'datetime',
        ];
    }

    // protected static function newFactory(): UserFactory
    // {
    // return UserFactory::new();
    // }

    /*
    |--------------------------------------------------------------------------
    |  Filament
    |--------------------------------------------------------------------------
    |
    */

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

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_roles')
            ->withPivot('assigned_by')
            ->withTimestamps();
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    public function teachers(): HasMany
    {
        return $this->hasMany(Teacher::class);
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class);
    }

    public function logs(): MorphMany
    {
        return $this->morphMany(ActivityLog::class, 'loggable', 'loggable_type', 'loggable_id');
    }

    public function images(): MorphMany
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
        if (! $this->relationLoaded('roles')) {
            $this->load('roles:id,name');
        }

        return $this->roles->pluck('name')->map(fn ($r) => strtolower($r))->contains(strtolower($role));
    }

    public function hasAnyRole(array $roles): bool
    {
        if (! $this->relationLoaded('roles')) {
            $this->load('roles:id,name');
        }

        $lowerRoles = array_map('strtolower', $roles);

        return $this->roles->pluck('name')->map(fn ($r) => strtolower($r))->intersect($lowerRoles)->isNotEmpty();
    }

    public function hasPermission(string $permission): bool
    {
        if (! $this->relationLoaded('roles')) {
            $this->load('roles.permissions:id,name');
        }

        $rolePermissions = $this->roles->flatMap->permissions->pluck('name')->map(fn ($p) => strtolower($p));

        return $rolePermissions->contains(strtolower($permission));
    }

    public function getRoles(): array
    {
        if (! $this->relationLoaded('roles')) {
            $this->load('roles:id,name');
        }

        return $this->roles->pluck('name')->toArray();
    }

    public function getPermissions(): array
    {
        if (! $this->relationLoaded('permissions')) {
            $this->load('roles.permissions:id,name');
        }

        return $this->roles->flatMap->permissions->pluck('name')->toArray();
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    #[Scope]
    public function byRole(Builder $query, array | string $roles): Builder
    {
        return $query->whereHas(
            'roles',
            fn ($q) => $q->whereIn('name', (array) $roles)
        );
    }

    #[Scope]
    public function byStatus(Builder $query, array | string $status): Builder
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
        return Attribute::make(
            get: fn () => $this->relationLoaded('status') && $this->status
                ? $this->status->name
                : null
        );
    }
}
