<?php

namespace Modules\Domain\database\seeders\Classroom;

use Exception;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClassroomSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $records = [
            [
                'teacher_id' => 1,
                'subject_id' => 1,
                'start_at' => 'Sample start_at 1',
                'end_at' => 'Sample end_at 1',
                'lat' => 'Sample lat 1',
                'lng' => 'Sample lng 1',
                'radius' => 'Sample radius 1',
            ],
            [
                'teacher_id' => 2,
                'subject_id' => 2,
                'start_at' => 'Sample start_at 2',
                'end_at' => 'Sample end_at 2',
                'lat' => 'Sample lat 2',
                'lng' => 'Sample lng 2',
                'radius' => 'Sample radius 2',
            ],
            [
                'teacher_id' => 3,
                'subject_id' => 3,
                'start_at' => 'Sample start_at 3',
                'end_at' => 'Sample end_at 3',
                'lat' => 'Sample lat 3',
                'lng' => 'Sample lng 3',
                'radius' => 'Sample radius 3',
            ],
            [
                'teacher_id' => 4,
                'subject_id' => 4,
                'start_at' => 'Sample start_at 4',
                'end_at' => 'Sample end_at 4',
                'lat' => 'Sample lat 4',
                'lng' => 'Sample lng 4',
                'radius' => 'Sample radius 4',
            ],
            [
                'teacher_id' => 5,
                'subject_id' => 5,
                'start_at' => 'Sample start_at 5',
                'end_at' => 'Sample end_at 5',
                'lat' => 'Sample lat 5',
                'lng' => 'Sample lng 5',
                'radius' => 'Sample radius 5',
            ],
        ];

        data_set($records, '*.created_at', $now);
        data_set($records, '*.updated_at', $now);

        try {
            DB::table('classrooms')->upsert(
                ['id'],
                [
                    'teacher_id',
                    'subject_id',
                    'start_at',
                    'end_at',
                    'lat',
                    'lng',
                    'radius',
                    'created_by',
                    'updated_by',
                    'deleted_by',
                    'updated_at',
                    'deleted_at',
                ]
            );
        } catch (Exception $e) {
            echo 'Failed to seed ClassroomSeeder!';
        }
    }
}
