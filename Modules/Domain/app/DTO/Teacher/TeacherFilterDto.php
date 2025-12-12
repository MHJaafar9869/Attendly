<?php

declare(strict_types=1);

namespace Modules\Domain\DTO\Teacher;

final readonly class TeacherFilterDto
{
    public function __construct(
        public readonly ?string $gender,
        public readonly ?string $status,
        public readonly ?string $department,
        public readonly ?string $searchText,
        public readonly ?string $orderBy,
        public readonly ?string $orderDir,
        public readonly ?int $page,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            status: $data['status'] ?? null,
            department: $data['department'] ?? null,
            searchText: $data['search_text'] ?? null,
            orderBy: $data['order_by'] ?? 'created_at',
            orderDir: $data['order_dir'] ?? 'desc',
            gender: $data['gender'] ?? null,
            page: $data['page'] ?? 1,
        );
    }
}
