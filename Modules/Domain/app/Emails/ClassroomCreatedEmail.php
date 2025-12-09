<?php

namespace Modules\Domain\Emails;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Modules\Core\Models\User;
use Modules\Domain\Models\Classroom;

class ClassroomCreatedEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(Classroom $classroom, User $user) {}

    /**
     * Build the message.
     */
    public function build(): self
    {
        return $this->view('view.name');
    }
}
