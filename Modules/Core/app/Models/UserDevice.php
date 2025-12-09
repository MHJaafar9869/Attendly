<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// use Modules\Core\Database\Factories\UserDeviceFactory;

class UserDevice extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'device_name',
        'device_token',
        'last_used_at',
    ];

    protected function casts(): array
    {
        return [
            'last_used_at' => 'datetime',
        ];
    }

    // protected static function newFactory(): UserDeviceFactory
    // {
    //     // return UserDeviceFactory::new();
    // }

    /*
    |--------------------------------------------------------------------------
    | Tracking
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

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    #[Scope]
    public function active(Builder $query, int $days = 30): Builder
    {
        return $query->where('last_used_at', '>=', now()->subDays($days));
    }

    #[Scope]
    public function inactive(Builder $query, int $days = 30): Builder
    {
        return $query->where('last_used_at', '<', now()->subDays($days))
            ->orWhereNull('last_used_at');
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function updateLastUsed()
    {
        return $this->touch('last_used_at');
    }
}
