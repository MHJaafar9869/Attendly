<?php

namespace App\Traits;

use App\Observers\AuditObserver;

trait HasUserStamps
{
    public static function bootHasUserStamps(): void
    {
        static::observe(AuditObserver::class);
    }
}
