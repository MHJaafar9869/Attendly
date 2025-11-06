<?php

namespace Modules\Domain\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// use Modules\Domain\Database\Factories\GovernorateFactory;

class Governorate extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'iso_code',
    ];

    // protected static function newFactory(): GovernorateFactory
    // {
    //     // return GovernorateFactory::new();
    // }
}
