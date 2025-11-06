<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Core\Observers\LogObserver;

// use Modules\Core\Database\Factories\CommentFactory;

#[ObservedBy(LogObserver::class)]
class Comment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'content', 'user_id', 'is_flagged',
        'flagged_by', 'flagged_at', 'parent_id',
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
    //     // return CommentFactory::new();
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
