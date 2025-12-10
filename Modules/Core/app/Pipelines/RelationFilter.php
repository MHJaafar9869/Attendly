<?php

declare(strict_types=1);

namespace Modules\Core\Pipelines;

use Closure;
use Illuminate\Database\Eloquent\Builder;

class RelationFilter
{
    public function __construct(
        private ?string $value,
        private ?string $relation,
        private array | string | null $columns = null,
        private ?string $context = null,
    ) {
        //
    }

    public function __invoke(Builder $query, Closure $next)
    {
        if (\is_string($this->value) && $this->relation && $this->columns) {
            $query->whereHas($this->relation, function ($q) {
                $q->whereAny($this->columns, 'LIKE', "%{$this->value}%");
                if ($this->context) {
                    $q->where('context', $this->context);
                }
            });
        }

        return $next($query);
    }
}
