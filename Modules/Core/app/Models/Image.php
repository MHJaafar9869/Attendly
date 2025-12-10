<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

// use Modules\Core\Database\Factories\ImageFactory;

/**
 * @property-read int $id
 * @property-read string $imageable_id
 * @property-read string $imageable_type
 * @property-read string $disk
 * @property-read string $image_path
 * @property-read string $image_url
 * @property-read string $image_mime
 * @property-read string|null $image_alt
 * @property-read string $type
 * @property-read bool $is_flagged
 * @property-read string|null $flagged_by
 * @property-read Carbon|null $flagged_at
 * @property-read Carbon $created_at
 * @property-read Carbon $updated_at
 * @property-read Carbon|null $deleted_at
 * @property-read Model|Collection $imageable  // The polymorphic relation to the parent model
 */
class Image extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'disk',
        'image_path',
        'image_url',
        'type',
        'image_mime',
        'image_alt',
        'is_flagged',
        'flagged_by',
        'flagged_at',
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
}
