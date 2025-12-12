<?php

declare(strict_types=1);

namespace Modules\Core\Pipelines;

use Closure;
use Illuminate\Database\Eloquent\Builder;

class BooleanFilter
{
    public function __construct(
        private ?string $column,
        private ?bool $value
    ) {
        //
    }

    public function __invoke(Builder $query, Closure $next)
    {
        if (\is_bool($this->value) && \is_string($this->column)) {
            $query->where($this->column, $this->value);
        }

        return $next($query);
    }
}
