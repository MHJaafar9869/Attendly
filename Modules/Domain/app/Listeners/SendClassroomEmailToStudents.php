<?php

namespace Modules\Domain\Listeners;

// use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use Modules\Domain\Emails\ClassroomCreatedEmail;
use Modules\Domain\Events\ClassroomCreated;
use Modules\Domain\Models\Student;

class SendClassroomEmailToStudents implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct() {}

    /**
     * Handle the event.
     */
    public function handle(ClassroomCreated $event): void
    {
        if (empty($event->studentIds)) {
            return;
        }

        Student::query()
            ->with('user:id,email,name')
            ->whereIn('id', $event->studentIds)
            ->chunk(100, function ($students) use ($event) {
                foreach ($students as $student) {
                    Mail::to($student->user->email)->queue(new ClassroomCreatedEmail($event->classroom, $student->user));
                }
            });

    }
}
