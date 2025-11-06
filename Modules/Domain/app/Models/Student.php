<?php

namespace Modules\Domain\Models;

use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Core\Models\User;
use Modules\Core\Observers\LogObserver;

// use Modules\Domain\Database\Factories\StudentFactory;

#[ObservedBy([LogObserver::class])]
final class Student extends Model
{
    use HasFactory;
    use HasUlids;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'student_code',
        'gender',

        'hashed_national_id',
        'academic_level_id',

        'address',
        'city',
        'governorate_id',

        'warning_count',
        'is_banned',
    ];

    protected function casts(): array
    {
        return [
            'hashed_national_id' => 'hashed',
            'phone' => 'encrypted',
            'secondary_phone' => 'encrypted',
            'address' => 'encrypted',
            'is_banned' => 'boolean',
        ];
    }

    // protected static function newFactory(): StudentFactory
    // {
    //     // return StudentFactory::new();
    // }

    /*
    |--------------------------------------------------------------------------
    | Logs
    |--------------------------------------------------------------------------
    */

    protected function trackables(): array
    {
        return ['all'];
    }

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function governorate(): BelongsTo
    {
        return $this->belongsTo(Governorate::class);
    }

    public function classrooms(): BelongsToMany
    {
        return $this->belongsToMany(Classroom::class, 'student_classrooms')
            ->withPivot('attended', 'is_late')
            ->withTimestamps();
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    #[Scope]
    public function byGender(Builder $query, string $gender): Builder
    {
        return in_array($gender, ['male', 'female'])
            ? $query->where('gender', $gender)
            : $query;
    }

    #[Scope]
    public function byCity(Builder $query, string $city): Builder
    {
        if ($city === '' || $city === '0') {
            return $query;
        }

        return $query->where('city', $city);
    }

    #[Scope]
    public function byGovernorate(Builder $query, int | string $governorateId): Builder
    {
        if (! $governorateId) {
            return $query;
        }

        return $query->where('governorate_id', $governorateId);
    }

    /*
    |--------------------------------------------------------------------------
    | Attributes
    |--------------------------------------------------------------------------
    */

    public function username(): Attribute
    {
        return Attribute::make(get: fn () => "{$this->user->first_name} {$this->user->last_name}" ?? null);
    }
}
