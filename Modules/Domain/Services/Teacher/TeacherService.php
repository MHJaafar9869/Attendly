<?php

declare(strict_types=1);

namespace Modules\Domain\Services\Teacher;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Pipeline;
use Modules\Core\DTO\Elequent\PaginateDto;
use Modules\Core\DTO\ResponseDto\ServiceResponseDto;
use Modules\Core\Pipelines\OrderByFilter;
use Modules\Core\Pipelines\RelationFilter;
use Modules\Core\Pipelines\SearchFilter;
use Modules\Core\Services\BaseService;
use Modules\Domain\DTO\Teacher\TeacherFilterDto;
use Modules\Domain\Repositories\Teacher\TeacherRepositoryInterface;
use Modules\Domain\Transformers\Teacher\TeacherResource;

final readonly class TeacherService extends BaseService
{
    public function __construct(
        protected TeacherRepositoryInterface $teacherRepo
    ) {}

    public function getTeachers(PaginateDto $dto, string $key): ServiceResponseDto
    {
        $response = Cache::flexible(
            key: "teachers:{$key}",
            ttl: [30, 60],
            callback: fn () => $this->teacherRepo->paginateWithRelations($dto, [
                'user:id,fullname',
                'status:id,name',
                'department:id,name',
            ])
        );

        return ServiceResponseDto::success()
            ->setMessage('Teachers list retrieved successfully')
            ->setData(TeacherResource::collection($response))
            ->setStatus(200);
    }

    public function getTeacher(string $id): ServiceResponseDto
    {
        $teacher = Cache::flexible(
            key: "teachers:{$id}",
            ttl: [30, 60],
            callback: fn () => $this->teacherRepo->findWithRelations($id, [
                'user:id,fullname',
                'status:id,name',
                'department:id,name',
            ]),
        );

        if (! $teacher) {
            return ServiceResponseDto::error("Invalid ID: {$id}", 404);
        }

        return ServiceResponseDto::success()
            ->setMessage('Teacher retrieved successfully')
            ->setData(TeacherResource::make($teacher))
            ->setStatus(200);
    }

    public function searchTeachers(TeacherFilterDto $dto, string $key): ServiceResponseDto
    {
        $query = $this->teacherRepo->allWithRelations(
            [
                'user:id,fullname',
                'status:id,name,text_color,bg_color',
                'department:id,name',
            ]
        );

        $pipes = [
            new RelationFilter($dto->gender, 'user', 'gender'),
            new RelationFilter($dto->status, 'status', 'name'),
            new RelationFilter($dto->department, 'department', 'name'),
            new SearchFilter($dto->searchText, 'teacher_code'),
            new OrderByFilter($dto->orderBy, $dto->orderDir),
        ];

        $cachecd = Cache::flexible(
            key: "teachers:{$key}:{$dto->page}",
            ttl: [10, 30],
            callback: fn () => Pipeline::send($query)
                ->through($pipes)
                ->thenReturn()
                ->paginate(15, ['*'], 'teachers', $dto->page)
        );

        return ServiceResponseDto::success()
            ->setData($cachecd)
            ->setStatus(200)
            ->setMessage('Students retrieved successfully');
    }
}
