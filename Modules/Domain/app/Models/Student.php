<?php

namespace Modules\Domain\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

// use Modules\Domain\Database\Factories\StudentFactory;

class Student extends Model
{
    use HasFactory;
    use HasUlids;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'student_code',
        'hashed_national_id',
        'gender',
        'academic_year',
        'section',
        'phone',
        'secondary_phone',
        'address',
        'city',
        'governorate_id',
    ];

    protected function casts(): array
    {
        return [
            'hashed_national_id' => 'hashed',
            'phone' => 'encrypted',
            'secondary_phone' => 'encrypted',
            'address' => 'encrypted',
        ];
    }

    // protected static function newFactory(): StudentFactory
    // {
    //     // return StudentFactory::new();
    // }
}
