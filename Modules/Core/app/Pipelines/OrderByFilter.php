<?php

declare(strict_types=1);

namespace Modules\Core\Pipelines;

use Closure;
use Illuminate\Database\Eloquent\Builder;

class OrderByFilter
{
    public function __construct(
        private ?string $orderBy,
        private ?string $direction = 'desc'
    ) {
        //
    }

    public function __invoke(Builder $query, Closure $next)
    {
        if (is_string($this->orderBy) && in_array($this->direction, ['asc', 'desc'])) {
            $query->orderBy($this->orderBy, $this->direction);
        }

        return $next($query);
    }
}
