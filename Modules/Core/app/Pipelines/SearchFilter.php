<?php

declare(strict_types=1);

namespace Modules\Core\Pipelines;

use Closure;
use Illuminate\Database\Eloquent\Builder;

class SearchFilter
{
    public function __construct(
        private ?string $value,
        private array | string | null $columns = null
    ) {
        //
    }

    public function __invoke(Builder $query, Closure $next)
    {
        if (is_string($this->value) && $this->columns) {
            $columns = (array) $this->columns;

            $query->where(function ($q) use ($columns) {
                $q->whereAny($columns, 'LIKE', "%{$this->value}%");
            });
        }

        return $next($query);
    }
}
