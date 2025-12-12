<?php

declare(strict_types=1);

namespace Modules\Domain\Services\Classroom;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Pipeline;
use Modules\Core\DTO\ResponseDto\ServiceResponseDto;
use Modules\Core\Pipelines\DateRangeFilter;
use Modules\Core\Pipelines\OrderByFilter;
use Modules\Core\Pipelines\RelationFilter;
use Modules\Core\Services\BaseService;
use Modules\Domain\DTO\Classroom\ClassroomFilterDto;
use Modules\Domain\DTO\Classroom\CreateClassroomDto;
use Modules\Domain\DTO\Classroom\UpdateClassroomDto;
use Modules\Domain\Repositories\Classroom\ClassroomRepositoryInterface;
use Modules\Domain\Transformers\Classroom\ClassroomResource;
use Throwable;

final readonly class ClassroomService extends BaseService
{
    public function __construct(
        protected ClassroomRepositoryInterface $classroomRepo
    ) {}

    public function getClass(int | string $id): ServiceResponseDto
    {
        try {
            $class = Cache::flexible(
                key: "classrooms:{$id}",
                ttl: [30, 60],
                callback: fn () => $this->classroomRepo->findWithRelations($id, [
                    'teacher:id,teacher_code',
                    'teacher.user:id,fullname',
                    'subject:id,name',
                    'students',
                ])
            );

            if (! $class) {
                return ServiceResponseDto::error("Not Found, Invalid ID: {$id}", 404);
            }

            return ServiceResponseDto::success()
                ->setMessage('Classroom retrieved successfully')
                ->setData(ClassroomResource::make($class))
                ->setStatus(200);
        } catch (Throwable $th) {
            return $this->getErrorResponse($th, 500);
        }
    }

    public function create(CreateClassroomDto $dto): ServiceResponseDto
    {
        try {
            $response = $this->classroomRepo->createClassroom($dto);
        } catch (Throwable $th) {
            return $this->getErrorResponse($th, 500);
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
        } catch (Throwable $th) {
            return $this->getErrorResponse($th, 500);
        }

        return ServiceResponseDto::success()
            ->setMessage($response->message)
            ->setData(ClassroomResource::make($response->data))
            ->setStatus($response->statusCode);
    }

    public function searchClassrooms(ClassroomFilterDto $dto, string $key): ServiceResponseDto
    {
        $query = $this->classroomRepo->allWithRelations(
            [
                'teacher:id,user_id',
                'subject:id,name',
                'students:id,student_code',
                'students.user:id,first_name',
            ]
        );

        $pipes = [
            new RelationFilter('teacher', $dto->teacher, 'teacher_code'),
            new RelationFilter('subject', $dto->subject, 'name'),
            new DateRangeFilter('start_at', $dto->startMin, $dto->startMax),
            new DateRangeFilter('end_at', $dto->endMin, $dto->endMax),
            new OrderByFilter($dto->orderBy, $dto->orderDir),
        ];

        $cachecd = Cache::flexible(
            key: "classrooms:{$key}:{$dto->page}",
            ttl: [10, 30],
            callback: fn () => Pipeline::send($query)
                ->through($pipes)
                ->thenReturn()
                ->paginate(15, ['*'], 'classrooms', $dto->page)
        );

        return ServiceResponseDto::success()
            ->setMessage('Classrooms retrieved successfully')
            ->setData($cachecd)
            ->setStatus(200);
    }
}
