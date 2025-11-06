<?php

namespace Modules\Core\database\seeders\Status;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatusSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $userStatuses = [
            [
                'name' => 'pending_verification',
                'context' => 'user',
                'bg_color' => '#f59e0b', // amber
                'text_color' => '#78350f', // darker amber
                'description' => 'User has registered but not yet verified email or OTP.',
            ],
            [
                'name' => 'active',
                'context' => 'user',
                'bg_color' => '#10b981', // green
                'text_color' => '#064e3b', // darker green
                'description' => 'User account is verified and fully active.',
            ],
            [
                'name' => 'inactive',
                'context' => 'user',
                'bg_color' => '#6b7280', // gray
                'text_color' => '#1f2937', // dark gray
                'description' => 'User account exists but has been temporarily deactivated.',
            ],
            [
                'name' => 'suspended',
                'context' => 'user',
                'bg_color' => '#ef4444', // red
                'text_color' => '#7f1d1d', // deep red
                'description' => 'User account is suspended due to policy violation or admin action.',
            ],
            [
                'name' => 'banned',
                'context' => 'user',
                'bg_color' => '#7f1d1d', // dark red
                'text_color' => '#450a0a', // darker variant
                'description' => 'User is permanently banned and cannot log in again.',
            ],
        ];

        $statuses = [
            ...$userStatuses,
        ];

        data_set($statuses, '*.created_at', $now);
        data_set($statuses, '*.updated_at', $now);

        DB::table('statuses')->upsert(
            $statuses,
            ['name', 'context'],
            ['context', 'bg_color', 'text_color', 'description', 'updated_at']
        );
    }
}
