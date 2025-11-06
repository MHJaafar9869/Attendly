<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Core\DTO\User\ImageUploadData;

// use Modules\Core\Database\Factories\ImageFactory;

class Image extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'disk', 'image_path', 'image_url', 'type',
        'image_mime', 'image_alt', 'is_flagged',
        'flagged_by', 'flagged_at',
    ];

    protected function casts(): array
    {
        return [
            'is_flagged' => 'boolean',
            'flagged_at' => 'datetime',
        ];
    }

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

    /*
    |--------------------------------------------------------------------------
    |  Helpers
    |--------------------------------------------------------------------------
    |
    */

    public static function fromDTO(ImageUploadData $data): self
    {
        return new self([
            'image_path' => $data->path,
            'type' => $data->type,
            'disk' => $data->disk,
            'image_url' => $data->url,
            'image_mime' => $data->mime,
            'image_alt' => $data->alt,
        ]);
    }
}
