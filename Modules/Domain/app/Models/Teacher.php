<?php

namespace Modules\Domain\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// use Modules\Domain\Database\Factories\TeacherFactory;

class Teacher extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    // protected static function newFactory(): TeacherFactory
    // {
    //     // return TeacherFactory::new();
    // }
}
