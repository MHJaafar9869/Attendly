<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Core\Observers\AuditObserver;
use Modules\Core\Observers\LogObserver;

// use Modules\Core\Database\Factories\SettingFactory;

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
