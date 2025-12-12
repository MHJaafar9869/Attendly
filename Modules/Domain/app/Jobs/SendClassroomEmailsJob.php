<?php

declare(strict_types=1);

namespace Modules\Domain\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Modules\Domain\Emails\ClassroomEmail;
use Modules\Domain\Models\Classroom;
use Modules\Domain\Models\Student;

class SendClassroomEmailsJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public readonly int $classroomId,
        public readonly array $studentIds = []
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $classroom = Classroom::query()->find($this->classroomId);

        if (! $classroom) {
            return;
        }

        Student::query()
            ->with('user:id,email,first_name')
            ->whereIn('id', $this->studentIds)
            ->chunk(100, function ($students) use ($classroom) {
                foreach ($students as $student) {
                    $user = $student->user;

                    if (! $user) {
                        continue;
                    }

                    Mail::to($user)->queue(new ClassroomEmail($classroom, $user));
                }
            });
    }
}
