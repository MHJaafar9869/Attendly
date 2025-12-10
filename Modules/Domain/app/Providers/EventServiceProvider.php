<?php

namespace Modules\Domain\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\Domain\Events\ClassroomCreated;
use Modules\Domain\Events\ClassroomUpdated;
use Modules\Domain\Listeners\ClassroomListener;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event handler mappings for the application.
     *
     * @var array<string, array<int, string>>
     */
    protected $listen = [
        ClassroomCreated::class => [
            ClassroomListener::class,
        ],
        ClassroomUpdated::class => [
            ClassroomListener::class,
        ],
    ];

    /**
     * Indicates if events should be discovered.
     *
     * @var bool
     */
    protected static $shouldDiscoverEvents = true;

    /**
     * Configure the proper event listeners for email verification.
     */
    protected function configureEmailVerification(): void {}
}
