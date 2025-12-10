<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Modules\Core\Observers\LogObserver;

// use Modules\Core\Database\Factories\StatusFactory;

/**
 * @property-read int $id
 * @property-read string $name
 * @property-read string $context
 * @property-read string $text_color
 * @property-read string $bg_color
 * @property-read string $description
 * @property-read Carbon $created_at
 * @property-read Carbon $updated_at
 */
#[ObservedBy(LogObserver::class)]
class Status extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'context',
        'text_color',
        'bg_color',
        'description',
    ];

    // protected static function newFactory(): StatusFactory
    // {
    // return StatusFactory::new();
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
}
