<?php

namespace Modules\Domain\database\seeders\Teacher;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TeacherSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $records = [
            [
                'user_id' => '01k983wphxy4wr8mym6n3s6m6d',
                'teacher_code' => 'Sample teacher_code 1',
                'teacher_type_id' => 1,
                'status_id' => 4,
                'approved_by' => 'Sample approved_by 1',
                'deleted_at' => 'Sample deleted_at 1',
            ],
            [
                'user_id' => '01k983wpwtdyhysbezjx764bg4',
                'teacher_code' => 'Sample teacher_code 2',
                'teacher_type_id' => 2,
                'status_id' => 5,
                'approved_by' => 'Sample approved_by 2',
                'deleted_at' => 'Sample deleted_at 2',
            ],
            [
                'user_id' => '01k983wpqa26fzpzfx342yhsvk',
                'teacher_code' => 'Sample teacher_code 3',
                'teacher_type_id' => 3,
                'status_id' => 1,
                'approved_by' => 'Sample approved_by 3',
                'deleted_at' => 'Sample deleted_at 3',
            ],
            [
                'user_id' => '01k983wpqa26fzpzfx342yhsvk',
                'teacher_code' => 'Sample teacher_code 4',
                'teacher_type_id' => 4,
                'status_id' => 3,
                'approved_by' => 'Sample approved_by 4',
                'deleted_at' => 'Sample deleted_at 4',
            ],
            [
                'user_id' => '01k983wphxy4wr8mym6n3s6m6d',
                'teacher_code' => 'Sample teacher_code 5',
                'teacher_type_id' => 5,
                'status_id' => 2,
                'approved_by' => 'Sample approved_by 5',
                'deleted_at' => 'Sample deleted_at 5',
            ],
        ];

        data_set($records, '*.created_at', $now);
        data_set($records, '*.updated_at', $now);

        DB::table('teachers')->upsert(
            $records,
            ['user_id', 'teacher_code'],
            ['user_id', 'teacher_code', 'teacher_type_id', 'status_id', 'approved_by', 'updated_at', 'deleted_at']
        );
    }
}
