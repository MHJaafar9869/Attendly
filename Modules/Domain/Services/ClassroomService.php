<?php

declare(strict_types=1);

namespace Modules\Domain\Services;

use Illuminate\Support\Facades\Cache;
use Modules\Core\DTO\ResponseDto\ServiceResponseDto;
use Modules\Domain\DTO\Student\CreateClassroomDto;
use Modules\Domain\DTO\Student\UpdateClassroomDto;
use Modules\Domain\Repositories\Classroom\ClassroomRepositoryInterface;
use Modules\Domain\Transformers\Classroom\ClassroomResource;

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
        try {
            $response = $this->classroomRepo->createClassroom($dto);
        } catch (\Throwable $th) {
            return ServiceResponseDto::error('Error occurred. Please try again', 500);
        }

        return ServiceResponseDto::success()
            ->setMessage($response->message)
            ->setData(ClassroomResource::make($response->data))
            ->setStatus($response->statusCode);
    }

    public function update(UpdateClassroomDto $dto): ServiceResponseDto
    {
        try {
            $response = $this->classroomRepo->updateClassroom($dto);
        } catch (\Throwable $th) {
            return ServiceResponseDto::error('Error occurred. Please try again', 500);
        }

        return ServiceResponseDto::success()
            ->setMessage($response->message)
            ->setData(ClassroomResource::make($response->data))
            ->setStatus($response->statusCode);
    }
}
