<?php

declare(strict_types=1);

namespace Modules\Core\Pipelines;

use Closure;
use Illuminate\Database\Eloquent\Builder;

class CreatedByFilter
{
    public function __construct(
        private ?int $value
    ) {
        //
    }

    public function __invoke(Builder $query, Closure $next)
    {
        if (is_int($this->value)) {
            $query->whereHas('createdBy', function ($q) {
                $q->where('created_by', $this->value);
            });
        }

        return $next($query);
    }
}
