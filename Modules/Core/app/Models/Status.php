<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// use Modules\Core\Database\Factories\StatusFactory;

class Status extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'context',
        'bg_color',
        'text_color',
    ];

    // protected static function newFactory(): StatusFactory
    // {
    //     // return StatusFactory::new();
    // }
}
