<?php

namespace App\Pipelines;

use Closure;
use Illuminate\Database\Eloquent\Builder;

class StatusFilter
{
    public function __construct(
        private ?string $value,
        private ?string $context = null
    ) {
        //
    }

    public function __invoke(Builder $query, Closure $next)
    {
        if ($this->value) {
            $query->whereHas('status', function ($q) {
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
