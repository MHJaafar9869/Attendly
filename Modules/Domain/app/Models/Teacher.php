<?php

namespace Modules\Domain\Models;

use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Modules\Core\Models\Status;
use Modules\Core\Models\Type;
use Modules\Core\Models\User;
use Modules\Core\Observers\LogObserver;

// use Modules\Domain\Database\Factories\TeacherFactory;

#[ObservedBy(LogObserver::class)]
class Teacher extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'teacher_code',
        'teacher_type_id',
        'status_id',
        'approved_by',
    ];

    // protected static function newFactory(): TeacherFactory
    // {
    //     // return TeacherFactory::new();
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
    | Relations
    |--------------------------------------------------------------------------
    */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(Type::class, 'teacher_type_id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function departments(): BelongsToMany
    {
        return $this->belongsToMany(Department::class, 'teacher_departments')
            ->withPivot(['assigned_by', 'role', 'assigned_at', 'unassigned_at'])
            ->withPivotValue('assigned_at', now())
            ->withTimestamps();
    }
}
