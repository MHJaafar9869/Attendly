<?php

declare(strict_types=1);

namespace Modules\Domain\Services;

use Illuminate\Support\Facades\Cache;
use Modules\Core\DTO\ResponseDto\ServiceResponseDto;
use Modules\Domain\DTO\Student\CreateClassroomDto;
use Modules\Domain\Repositories\Classroom\ClassroomRepositoryInterface;

final readonly class ClassroomService
{
    public function __construct(
        protected ClassroomRepositoryInterface $classroomRepo
    ) {}

    public function getClass(int|string $id): ServiceResponseDto
    {
        $class = Cache::flexible("classrooms:{$id}", [30, 60], fn () => $this->classroomRepo->findWithRelations($id, ['teacher', 'subject', 'students']));

        if (! $class) {
            return ServiceResponseDto::error('Classroom not found', 404);
        }

        return ServiceResponseDto::response($class);
    }

    public function create(CreateClassroomDto $dto): ServiceResponseDto
    {
        $class = $this->classroomRepo->createClassroom($dto);

        return ServiceResponseDto::response($class);
    }
}
