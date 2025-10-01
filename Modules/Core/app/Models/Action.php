<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// use Modules\Core\Database\Factories\ActionFactory;

class Action extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['name'];

    // protected static function newFactory(): ActionFactory
    // {
    //     // return ActionFactory::new();
    // }

    /*
    |--------------------------------------------------------------------------
    |  Relations
    |--------------------------------------------------------------------------
    |
    */

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_actions');
    }
}
