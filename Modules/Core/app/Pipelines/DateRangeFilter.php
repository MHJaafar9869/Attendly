<?php

declare(strict_types=1);

namespace Modules\Core\Pipelines;

use Closure;
use Illuminate\Database\Eloquent\Builder;

class DateRangeFilter
{
    public function __construct(
        private ?string $column,
        private ?string $fromDate = null,
        private ?string $toDate = null
    ) {
        //
    }

    public function __invoke(Builder $query, Closure $next)
    {
        if (is_string($this->column)) {
            if (is_string($this->fromDate) && is_string($this->toDate)) {
                $query->whereBetween($this->column, [$this->fromDate, $this->toDate]);
            } elseif (is_string($this->fromDate)) {
                $query->where($this->column, '>=', $this->fromDate);
            } elseif (is_string($this->toDate)) {
                $query->where($this->column, '<=', $this->toDate);
            }
        }

        return $next($query);
    }
}
