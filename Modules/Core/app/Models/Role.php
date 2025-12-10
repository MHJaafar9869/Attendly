<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

// use Modules\Core\Database\Factories\RoleFactory;

/**
 * @property-read int $id
 * @property-read string $name
 * @property-read Carbon $created_at
 * @property-read Carbon $updated_at
 */
class Role extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['name'];

    protected $hidden = ['pivot'];

    // protected static function newFactory(): RoleFactory
    // {
    // return RoleFactory::new();
    // }

    /*
    |--------------------------------------------------------------------------
    |  Relations
    |--------------------------------------------------------------------------
    |
    */

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permissions')
            ->withTimestamps();
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_roles');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    #[Scope]
    public function usersByRole(Builder $q, array|string $roles): Builder
    {
        return $q->with('users')->whereIn('name', (array) $roles);
    }
}
