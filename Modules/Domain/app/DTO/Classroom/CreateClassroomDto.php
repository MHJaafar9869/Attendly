<?php

declare(strict_types=1);

namespace Modules\Domain\DTO\Student;

class CreateClassroomDto
{
    public function __construct(
        public string $teacherId,
        public string|int $subjectId,
        public string $startAt,
        public string $endAt,
        public float $lat,
        public float $lng,
        public int $radius,
        public array $studentsIds
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
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
