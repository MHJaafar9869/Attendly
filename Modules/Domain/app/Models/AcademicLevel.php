<?php

namespace Modules\Domain\Models;

use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Core\Observers\LogObserver;

// use Modules\Domain\Database\Factories\AcademicLevelFactory;

#[ObservedBy(LogObserver::class)]
class AcademicLevel extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'year_number',
        'group_code',
        'display_name',
        'major_id',
    ];

    // protected static function newFactory(): AcademicLevelFactory
    // {
    //     // return AcademicLevelFactory::new();
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

    public function major(): BelongsTo
    {
        return $this->belongsTo(Major::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Scope
    |--------------------------------------------------------------------------
    */

    #[Scope]
    public function byMajor(Builder $query, string $major): Builder
    {
        return $query->whereHas(
            'major',
            fn ($q) => $q->where('code', $major)->orWhere('name', $major)
        );
    }

    #[Scope]
    public function byYear(Builder $query, int $year): Builder
    {
        return $query->where('year_number', $year);
    }

    #[Scope]
    public function byGroup(Builder $query, string $code): Builder
    {
        return $query->where('group_code', $code);
    }
}
