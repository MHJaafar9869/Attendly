<?php

declare(strict_types=1);

namespace Modules\Domain\DTO\Student;

class StudentFilterDto
{
    public function __construct(
        public ?string $searchText = null,
        public ?bool $isBanned = null,
        public ?string $gender = null,
        public ?bool $attended = null,
        private bool $paginate = false
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            searchText: $data['search_text'] ?? null,
            isBanned: $data['is_banned'] ?? null,
            gender: $data['gender'] ?? null,
            attended: $data['attended'] ?? null,
        );
    }

    public function setPaginate(bool $paginate): void
    {
        $this->paginate = $paginate;
    }
}
