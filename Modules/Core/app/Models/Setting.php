<?php

namespace Modules\Core\Models;

use App\Traits\HasUserStamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

// use Modules\Core\Database\Factories\SettingFactory;

class Setting extends Model
{
    use HasFactory, HasUserStamps, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'key',
        'value',
        'type',
        'description',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    // protected static function newFactory(): SettingFactory
    // {
    //     // return SettingFactory::new();
    // }

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
