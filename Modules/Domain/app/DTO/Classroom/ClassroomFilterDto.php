<?php

declare(strict_types=1);

namespace Modules\Domain\DTO\Classroom;

final readonly class ClassroomFilterDto
{
    public function __construct(
        public ?string $teacher = null,
        public ?string $subject = null,
        public ?string $startMin = null,
        public ?string $startMax = null,
        public ?string $endMin = null,
        public ?string $endMax = null,
        public ?string $orderBy = null,
        public ?string $orderDir = null,
        public ?int $page = 1
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            teacher: $data['teacher'] ?? null,
            subject: $data['subject'] ?? null,
            startMin: $data['start_min'] ?? null,
            startMax: $data['start_max'] ?? null,
            endMin: $data['end_min'] ?? null,
            endMax: $data['end_max'] ?? null,
            orderBy: $data['order_by'] ?? null,
            orderDir: $data['order_dir'] ?? null,
            page: $data['page'] ?? 1
        );
    }

    public function toArray(): array
    {
        return [
            'teacher' => $this->teacher,
            'subject' => $this->subject,
            'start_min' => $this->startMin,
            'start_max' => $this->startMax,
            'end_min' => $this->endMin,
            'end_max' => $this->endMax,
            'order_by' => $this->orderBy,
            'order_dir' => $this->orderDir,
            'page' => $this->page,
        ];
    }
}
