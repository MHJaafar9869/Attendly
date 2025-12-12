<?php

declare(strict_types=1);

namespace Modules\Domain\DTO\Teacher;

final readonly class TeacherFilterDto
{
    public function __construct(
        public ?string $gender,
        public ?string $status,
        public ?string $department,
        public ?string $searchText,
        public ?string $orderBy,
        public ?string $orderDir,
        public ?int $page,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            gender: $data['gender'] ?? null,
            status: $data['status'] ?? null,
            department: $data['department'] ?? null,
            searchText: $data['search_text'] ?? null,
            orderBy: $data['order_by'] ?? 'created_at',
            orderDir: $data['order_dir'] ?? 'desc',
            page: $data['page'] ?? 1,
        );
    }
}
