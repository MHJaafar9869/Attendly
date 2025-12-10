<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

// use Modules\Core\Database\Factories\PermissionFactory;

/**
 * @property-read int $id
 * @property-read string $name
 * @property-read Carbon $created_at
 * @property-read Carbon $updated_at
 */
class Permission extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['name'];

    protected $hidden = ['pivot'];

    // protected static function newFactory(): PermissionFactory
    // {
    //     // return PermissionFactory::new();
    // }

    /*
    |--------------------------------------------------------------------------
    |  Relations
    |--------------------------------------------------------------------------
    |
    */

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_permissions');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_permissions');
    }
}
