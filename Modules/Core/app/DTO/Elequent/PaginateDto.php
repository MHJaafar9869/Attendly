<?php

declare(strict_types=1);

namespace Modules\Core\DTO\Elequent;

class PaginateDto
{
    public function __construct(
        public int $perPage,
        public array | string | null $columns,
        public string $pageName
    ) {}

    public static function fromRequest(array $request): self
    {
        return new self(
            perPage: $request['per_page'] ?? 15,
            columns: $request['columns'] ?? ['*'],
            pageName: $request['page_name'] ?? 'page',
        );
    }

    public function toArray(): array
    {
        return [
            'per_page' => $this->perPage,
            'columns' => $this->columns,
            'page_name' => $this->pageName,
        ];
    }
}
