<?php

declare(strict_types=1);

namespace Modules\Core\Pipelines;

use Closure;
use Illuminate\Database\Eloquent\Builder;

class GenderFilter
{
    public function __construct(
        private ?string $value,
    ) {
        //
    }

    public function __invoke(Builder $query, Closure $next)
    {
        if (\is_string($this->value) && \in_array($this->value, ['male', 'female'])) {
            $query->where('gender', $this->value);
        }

        return $next($query);
    }
}
