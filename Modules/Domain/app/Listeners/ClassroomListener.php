<?php

namespace Modules\Domain\Listeners;

// use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Domain\Events\ClassroomCreatedEvent;
use Modules\Domain\Events\ClassroomUpdatedEvent;
use Modules\Domain\Jobs\SendClassroomEmailsJob;

class ClassroomListener implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct() {}

    /**
     * Handle the event.
     */
    public function handle(ClassroomCreatedEvent | ClassroomUpdatedEvent $event): void
    {
        if ($event->studentIds === []) {
            return;
        }

        SendClassroomEmailsJob::dispatch($event->classroomId, $event->studentIds);
    }
}
