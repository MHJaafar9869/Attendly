<?php

namespace App\Pipelines;

use Closure;
use Illuminate\Database\Eloquent\Builder;

class TypeFilter
{
    public function __construct(
        private ?string $value,
        private ?string $context = null,
        private ?string $relation = 'type'
    ) {
        //
    }

    public function __invoke(Builder $query, Closure $next)
    {
        if (is_string($this->value)) {
            $query->whereHas($this->relation, function ($q) {
                $q->where('id', $this->value)
                    ->orWhere('name', $this->value);
                if ($this->context) {
                    $q->where('context', $this->context);
                }
            });
        }

        return $next($query);
    }
}
