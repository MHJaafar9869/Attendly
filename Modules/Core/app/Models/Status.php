<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Core\Observers\LogObserver;

// use Modules\Core\Database\Factories\StatusFactory;

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
    //     // return StatusFactory::new();
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
