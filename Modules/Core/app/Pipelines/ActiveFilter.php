<?php

declare(strict_types=1);

namespace Modules\Core\Pipelines;

use Closure;
use Illuminate\Database\Eloquent\Builder;

class ActiveFilter
{
    public function __construct(
        private ?bool $value
    ) {
        //
    }

    public function __invoke(Builder $query, Closure $next)
    {
        if (is_bool($this->value)) {
            $query->where('is_active', $this->value);
        }

        return $next($query);
    }
}
