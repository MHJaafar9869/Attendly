<?php

declare(strict_types=1);

namespace Modules\Domain\Services\Student;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Pipeline;
use Modules\Core\DTO\Elequent\PaginateDto;
use Modules\Core\DTO\ResponseDto\ServiceResponseDto;
use Modules\Core\Pipelines\BooleanFilter;
use Modules\Core\Pipelines\RelationFilter;
use Modules\Core\Pipelines\SearchFilter;
use Modules\Core\Services\BaseService;
use Modules\Domain\DTO\Student\StudentFilterDto;
use Modules\Domain\Repositories\Student\StudentRepositoryInterface;
use Modules\Domain\Transformers\Student\StudentResource;

final readonly class StudentService extends BaseService
{
    public function __construct(
        protected readonly StudentRepositoryInterface $studentRepo
    ) {}

    public function getStudents(PaginateDto $dto, string $key): ServiceResponseDto
    {
        $response = Cache::flexible(
            key: "students:{$key}",
            ttl: [30, 60],
            callback: fn () => $this->studentRepo->paginateWithRelations($dto, [
                'user:id,fullname',
                'governorate:id,name,iso_code',
            ])
        );

        return ServiceResponseDto::success()
            ->setMessage('Students list retrieved successfully')
            ->setData(StudentResource::collection($response))
            ->setStatus(200);
    }

    public function getStudent(string $id): ServiceResponseDto
    {
        $student = Cache::flexible(
            key: "students:{$id}",
            ttl: [30, 60],
            callback: fn () => $this->studentRepo->findWithRelations($id, [
                'user:id,fullname',
                'governorate:id,name,iso_code',
                'classrooms:id,attended',
                'classrooms.subject:id,name',
                'classrooms.teacher.user:id,fullname',
            ]),
        );

        if (! $student) {
            return ServiceResponseDto::error("Invalid ID: {$id}", 404);
        }

        return ServiceResponseDto::success()
            ->setMessage('Student retrieved successfully')
            ->setData(StudentResource::make($student))
            ->setStatus(200);
    }

    public function searchStudents(StudentFilterDto $dto, string $key): ServiceResponseDto
    {
        $query = $this->studentRepo->allWithRelations(
            [
                'user',
                'governorate',
                'classrooms',
            ]
        );

        $pipes = [
            new SearchFilter($dto->searchText, ['student_code', 'address', 'city']),
            new BooleanFilter('is_banned', $dto->isBanned),
            new RelationFilter($dto->gender, 'user', 'gender'),
            new BooleanFilter('attended', $dto->attended),
        ];

        $cachecd = Cache::flexible(
            key: "students:{$key}:{$dto->page}",
            ttl: [10, 30],
            callback: fn () => Pipeline::send($query)
                ->through($pipes)
                ->thenReturn()
                ->paginate(15, ['*'], 'students', $dto->page)
        );

        return ServiceResponseDto::success()
            ->setData($cachecd)
            ->setStatus(200)
            ->setMessage('Students retrieved successfully');
    }
}
