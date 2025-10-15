<?php

namespace Modules\Core\Traits;

use Modules\Core\Observers\AuditObserver;

trait HasUserStamps
{
    public static function bootHasUserStamps(): void
    {
        static::observe(AuditObserver::class);
    }
}
