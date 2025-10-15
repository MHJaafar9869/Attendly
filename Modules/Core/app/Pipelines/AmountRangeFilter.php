<?php

declare(strict_types=1);

namespace Modules\Core\Pipelines;

use Closure;
use Illuminate\Database\Eloquent\Builder;

class AmountRangeFilter
{
    public function __construct(
        private ?string $column,
        private ?int $min,
        private ?int $max
    ) {
        //
    }

    public function __invoke(Builder $query, Closure $next)
    {
        if (is_string($this->column)) {
            if ((is_int($this->min) && $this->min > 0) && (is_int($this->max) && $this->max > 0)) {
                $query->whereBetween($this->column, [$this->min, $this->max]);
            } elseif (is_int($this->min) && $this->min > 0) {
                $query->where($this->column, '>=', $this->min);
            } elseif (is_int($this->max) && $this->max > 0) {
                $query->where($this->column, '<=', $this->max);
            }
        }

        return $next($query);
    }
}
