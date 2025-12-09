<?php

namespace Modules\Domain\database\seeders\Student;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Domain\Models\Governorate;
use Modules\Domain\Models\Student;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        $userIds = DB::table('users')
            ->orderBy('created_at')
            ->skip(1)
            ->take(3)
            ->pluck('id')
            ->toArray();

        $governorateIds = Governorate::query()->limit(5)->pluck('id')->toArray();

        $students = [
            [
                'user_id' => $userIds[0] ?? null,
                'student_code' => 'STU-001',
                'hashed_national_id' => '1',
                'gender' => 'female',
                'academic_year' => '2024/2025',
                'section' => 'A',
                'phone' => '01000000001',
                'secondary_phone' => '01000000011',
                'address' => '123 Nile Street',
                'city' => 'Cairo',
                'governorate_id' => $governorateIds[0] ?? null,
            ],
            [
                'user_id' => $userIds[1] ?? null,
                'student_code' => 'STU-002',
                'hashed_national_id' => '2',
                'gender' => 'male',
                'academic_year' => '2024/2025',
                'section' => 'B',
                'phone' => '01000000002',
                'secondary_phone' => '01000000012',
                'address' => '456 Tahrir Square',
                'city' => 'Giza',
                'governorate_id' => $governorateIds[1] ?? null,
            ],
            [
                'user_id' => $userIds[2] ?? null,
                'student_code' => 'STU-003',
                'hashed_national_id' => '3',
                'gender' => 'female',
                'academic_year' => '2024/2025',
                'section' => 'C',
                'phone' => '01000000003',
                'secondary_phone' => '01000000013',
                'address' => '789 Corniche Street',
                'city' => 'Alexandria',
                'governorate_id' => $governorateIds[2] ?? null,
            ],
        ];

        foreach ($students as $student) {
            Student::firstOrCreate(
                ['student_code' => $student['student_code']],
                $student
            );
        }
    }
}
