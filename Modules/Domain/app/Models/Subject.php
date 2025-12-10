<?php

namespace Modules\Domain\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Core\Observers\LogObserver;

// use Modules\Domain\Database\Factories\SubjectFactory;

/**
 * @property-read int $id
 * @property-read string $name
 * @property-read Carbon $created_at
 * @property-read Carbon $updated_at
 */
#[ObservedBy(LogObserver::class)]
class Subject extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['name'];

    // protected static function newFactory(): SubjectFactory
    // {
    //     // return SubjectFactory::new();
    // }

    /*
    |--------------------------------------------------------------------------
    | Logs
    |--------------------------------------------------------------------------
    */
    protected function trackables(): array
    {
        return ['name'];
    }
}
