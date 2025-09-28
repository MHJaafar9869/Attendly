<?php

namespace App\Pipelines;

use Closure;
use Illuminate\Database\Eloquent\Builder;

class SearchFilter
{
    public function __construct(
        private ?string $value,
        private array|string|null $columns = null
    ) {
        //
    }

    public function __invoke(Builder $query, Closure $next)
    {
        if (is_string($this->value) && $this->columns) {
            $columns = (array) $this->columns;

            $query->where(function ($q) use ($columns) {
                foreach ($columns as $idx => $column) {
                    $method = $idx === 0 ? 'whereLike' : 'orWhereLike';
                    $q->{$method}($column, "%{$this->value}%");
                }
            });
        }

        return $next($query);
    }
}
