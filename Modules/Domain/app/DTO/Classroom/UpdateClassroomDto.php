<?php

declare(strict_types=1);

namespace Modules\Domain\DTO\Classroom;

final readonly class UpdateClassroomDto
{
    public function __construct(
        public readonly int | string $id,
        public readonly ?string $teacherId = null,
        public readonly int | string | null $subjectId = null,
        public readonly ?string $startAt = null,
        public readonly ?string $endAt = null,
        public readonly ?float $lat = null,
        public readonly ?float $lng = null,
        public readonly ?int $radius = null,
        public readonly ?array $studentsIds = null
    ) {}

    public static function fromRequest(array $data, int | string $id): self
    {
        return new self(
            id: $id,
            teacherId: $data['teacher_id'],
            subjectId: $data['subject_id'],
            startAt: $data['start_at'],
            endAt: $data['end_at'],
            lat: $data['lat'],
            lng: $data['lng'],
            radius: $data['radius'],
            studentsIds: $data['students_ids'],
        );
    }

    public function toArray(): array
    {
        return [
            'teacher_id' => $this->teacherId,
            'subject_id' => $this->subjectId,
            'start_at' => $this->startAt,
            'end_at' => $this->endAt,
            'lat' => $this->lat,
            'lng' => $this->lng,
            'radius' => $this->radius,
        ];
    }
}
