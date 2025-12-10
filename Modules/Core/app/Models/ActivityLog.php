<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

// use Modules\Core\Database\Factories\ActivityLogFactory;

/**
 * @property-read int $id
 * @property-read string $user_id
 * @property-read int $action_type_id
 * @property-read int|string $loggable_id
 * @property-read string $loggable_type
 * @property-read string|null $ip_address
 * @property-read string|null $user_agent
 * @property-read array $meta
 * @property-read Carbon $created_at
 * @property-read Carbon $updated_at
 */
class ActivityLog extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'action_type_id',
        'ip_address',
        'user_agent',
        'meta',
    ];

    // protected static function newFactory(): ActivityLogFactory
    // {
    // return ActivityLogFactory::new();
    // }

    /*
    |--------------------------------------------------------------------------
    |  Relations
    |--------------------------------------------------------------------------
    |
    */

    public function loggable()
    {
        return $this->morphTo();
    }

    public function users()
    {
        return $this->belongsTo(User::class);
    }
}
