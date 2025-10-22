<?php

namespace Modules\Core\Traits;

use Modules\Core\Observers\LogObserver;

trait LogActivity
{
    public static function bootLogActivity(): void
    {
        static::observe(LogObserver::class);
    }
}
