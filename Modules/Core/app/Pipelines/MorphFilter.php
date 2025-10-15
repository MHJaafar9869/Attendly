<?php

declare(strict_types=1);

namespace Modules\Core\Pipelines;

use Closure;
use Illuminate\Database\Eloquent\Builder;

class MorphFilter
{
    public function __construct(
        private ?string $prefix,
        private ?string $morphType = null,
        private ?int $morphId = null
    ) {
        //
    }

    public function __invoke(Builder $query, Closure $next)
    {
        if (is_string($this->prefix) && is_string($this->morphType)) {
            $query->where("{$this->prefix}_type", $this->morphType);
            if (is_int($this->morphId)) {
                $query->where("{$this->prefix}_id", $this->morphId);
            }
        }

        return $next($query);
    }
}
