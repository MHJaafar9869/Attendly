<?php

declare(strict_types=1);

namespace Modules\Domain\DTO\Student;

final readonly class StudentFilterDto
{
    public function __construct(
        public ?string $searchText,
        public ?bool $isBanned,
        public ?string $gender,
        public ?bool $attended,
        public bool $paginate,
        public ?int $page
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            searchText: $data['search_text'] ?? null,
            isBanned: $data['is_banned'] ?? null,
            gender: $data['gender'] ?? null,
            attended: $data['attended'] ?? null,
            paginate: $data['paginate'] ?? false,
            page: $data['page'] ?? 1
        );
    }
}
