<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Modules\Core\Observers\LogObserver;

// use Modules\Core\Database\Factories\CommentFactory;

/**
 * @property-read int $id
 * @property-read int|string $commentable_id
 * @property-read string $commentable_type
 * @property-read string $content
 * @property-read string $user_id
 * @property-read bool $is_flagged
 * @property-read string|null $flagged_by
 * @property-read Carbon|null $flagged_at
 * @property-read int|null $parent_id
 * @property-read Carbon $created_at
 * @property-read Carbon $updated_at
 * @property-read Carbon|null $deleted_at
 */
#[ObservedBy(LogObserver::class)]
class Comment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'content',
        'user_id',
        'is_flagged',
        'flagged_by',
        'flagged_at',
        'parent_id',
    ];

    protected function casts(): array
    {
        return [
            'is_flagged' => 'boolean',
            'flagged_at' => 'timestamp',
        ];
    }

    // protected static function newFactory(): CommentFactory
    // {
    // return CommentFactory::new();
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

    /*
    |--------------------------------------------------------------------------
    |  Relations
    |--------------------------------------------------------------------------
    |
    */

    public function commentable()
    {
        return $this->morphTo();
    }

    public function poster()
    {
        return $this->belongsTo(User::class);
    }

    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function flagger()
    {
        return $this->belongsTo(User::class, 'flagged_by');
    }
}
