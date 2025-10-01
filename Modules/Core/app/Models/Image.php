<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// use Modules\Core\Database\Factories\ImageFactory;

class Image extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'disk', 'image_path', 'image_url',
        'image_mime', 'image_alt', 'is_flagged',
        'flagged_by', 'flagged_at',
    ];

    protected $casts = [
        'is_flagged' => 'boolean',
        'flagged_at' => 'datetime',
    ];

    // protected static function newFactory(): ImageFactory
    // {
    //     // return ImageFactory::new();
    // }

    /*
    |--------------------------------------------------------------------------
    |  Relations
    |--------------------------------------------------------------------------
    |
    */

    public function imageable()
    {
        return $this->morphTo();
    }

    public function flagger()
    {
        return $this->belongsTo(User::class, 'flagged_by');
    }
}
