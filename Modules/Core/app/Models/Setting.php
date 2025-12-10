<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Modules\Core\Observers\AuditObserver;
use Modules\Core\Observers\LogObserver;

// use Modules\Core\Database\Factories\SettingFactory;

/**
 * @property-read int $id
 * @property-read string $key
 * @property-read string $value
 * @property-read string $type
 * @property-read string|null $description
 * @property-read string|null $created_by
 * @property-read string|null $updated_by
 * @property-read string|null $deleted_by
 * @property-read Carbon $created_at
 * @property-read Carbon $updated_at
 * @property-read Carbon|null $deleted_at
 */
#[ObservedBy([LogObserver::class, AuditObserver::class])]
class Setting extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'key',
        'value',
        'type',
        'description',
    ];

    // protected static function newFactory(): SettingFactory
    // {
    //     // return SettingFactory::new();
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
    |  Relations
    |--------------------------------------------------------------------------
    |
    */

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updator()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function deletor()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }
}
