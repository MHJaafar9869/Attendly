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
use Illuminate\Support\Carbon;
use Modules\Core\Observers\AuditObserver;
use Modules\Core\Observers\LogObserver;

// use Modules\Domain\Database\Factories\ClassroomFactory;

/**
 * @property-read string $id
 * @property-read string $teacher_id
 * @property-read int $subject_id
 * @property-read Carbon $start_at
 * @property-read Carbon $end_at
 * @property-read float $lat
 * @property-read float $lng
 * @property-read int $radius
 * @property-read string|null $created_by
 * @property-read string|null $updated_by
 * @property-read string|null $deleted_by
 * @property-read Carbon $created_at
 * @property-read Carbon $updated_at
 * @property-read Carbon|null $deleted_at
 */
#[ObservedBy([LogObserver::class, AuditObserver::class])]
class Classroom extends Model
{
    use HasFactory;
    use HasUlids;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'teacher_id',
        'subject_id',
        'start_at',
        'end_at',
        'lat',
        'lng',
        'radius',
    ];

    protected function casts(): array
    {
        return [
            'start_at' => 'datetime',
            'end_at' => 'datetime',
            'lat' => 'float',
            'lng' => 'float',
            'radius' => 'integer',
        ];
    }

    // protected static function newFactory(): SessionFactory
    // {
    // return SessionFactory::new();
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

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'student_classrooms')
            ->withPivot(['attended'])
            ->withTimestamps();
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    #[Scope]
    public function upcomming(Builder $query): Builder
    {
        return $query->whereDate('start_at', '>', now());
    }

    #[Scope]
    public function past(Builder $query): Builder
    {
        return $query->whereDate('end_at', '<', now());
    }

    #[Scope]
    public function active(Builder $query): Builder
    {
        return $query->where('start_at', '<=', now())->where('end_at', '>=', now());
    }

    #[Scope]
    public function attended(Builder $query): Builder
    {
        return $query->whereHas('students', function ($q) {
            $q->wherePivot('attended', true);
        });
    }

    #[Scope]
    public function notAttended(Builder $query): Builder
    {
        return $query->whereHas('students', function ($q) {
            $q->wherePivot('attended', false);
        });
    }
    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function duration(): Attribute
    {
        return Attribute::make(
            get: fn (): ?float => ($this->start_at && $this->end_at)
                ? $this->end_at->diffInMinutes($this->start_at)
                : null
        );
    }

    public function location(): Attribute
    {
        return Attribute::make(get: fn (): array => ['lat' => $this->lat, 'lng' => $this->lng]);
    }

    public function subjectName(): Attribute
    {
        return Attribute::make(get: fn (): ?string => $this->subject->name);
    }
}
