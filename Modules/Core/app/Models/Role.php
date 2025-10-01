<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// use Modules\Core\Database\Factories\RoleFactory;

class Role extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['name'];

    // protected static function newFactory(): RoleFactory
    // {
    //     // return RoleFactory::new();
    // }

    /*
    |--------------------------------------------------------------------------
    |  Relations
    |--------------------------------------------------------------------------
    |
    */

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permissions');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_roles');
    }
}
