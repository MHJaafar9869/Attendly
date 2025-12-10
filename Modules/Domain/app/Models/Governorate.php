<?php

namespace Modules\Domain\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// use Modules\Domain\Database\Factories\GovernorateFactory;

/**
 * @property-read int $id
 * @property-read string $name
 * @property-read string $iso_code
 * @property-read Carbon $created_at
 * @property-read Carbon $updated_at
 */
class Governorate extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'iso_code',
    ];

    // protected static function newFactory(): GovernorateFactory
    // {
    // return GovernorateFactory::new();
    // }
}
