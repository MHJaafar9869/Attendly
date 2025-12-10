<?php

namespace Modules\Domain\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Modules\Core\Observers\LogObserver;

// use Modules\Domain\Database\Factories\DepartmentFactory;

/**
 * @property-read int $id
 * @property-read string $name
 * @property-read string|null $slug
 * @property-read string|null $description
 * @property-read Carbon $created_at
 * @property-read Carbon $updated_at
 */
#[ObservedBy(LogObserver::class)]
class Department extends Model
{
    use HasFactory;
    use HasUlids;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    // protected static function newFactory(): DepartmentFactory
    // {
    //     // return DepartmentFactory::new();
    // }

    /*
    |--------------------------------------------------------------------------
    | Logs
    |--------------------------------------------------------------------------
    */
    protected function trackables(): array
    {
        return [];
    }

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function teachers(): BelongsToMany
    {
        return $this->belongsToMany(Teacher::class, 'teacher_departments')
            ->withPivot(['assigned_by', 'role', 'assigned_at', 'unassigned_at'])
            ->withPivotValue('assigned_at', now())
            ->withTimestamps();
    }
}
