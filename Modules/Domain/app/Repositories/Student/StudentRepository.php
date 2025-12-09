<?php

declare(strict_types=1);

namespace Modules\Domain\Repositories\Student;

use App\Repositories\BaseRepository\BaseRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Pipeline;
use Modules\Core\DTO\ResponseDto\RepositoryResponseDto;
use Modules\Core\Models\User;
use Modules\Core\Pipelines\BooleanFilter;
use Modules\Core\Pipelines\GenderFilter;
use Modules\Core\Pipelines\SearchFilter;
use Modules\Domain\DTO\Student\StudentFilterDto;
use Modules\Domain\Models\Student;

final readonly class StudentRepository extends BaseRepository implements StudentRepositoryInterface
{
    public function __construct(Student $model, protected User $user)
    {
        parent::__construct($model);
    }

    public function paginatedStudents(int $perPage = 15, string $pageName = 'students'): LengthAwarePaginator
    {
        $relations = [
            'user',
            'governorate',
            'classrooms',
        ];

        return $this->paginateWithRelations($perPage, $pageName, $relations);
    }

    public function searchStudents(StudentFilterDto $dto): RepositoryResponseDto
    {
        $query = $this->allWithRelations(
            [
                'user',
                'governorate',
                'classrooms',
            ]
        );

        $pipes = [
            new SearchFilter($dto->searchText, ['student_code', 'address', 'city']),
            new BooleanFilter('is_banned', $dto->isBanned),
            new GenderFilter($dto->gender),
            new BooleanFilter('attended', $dto->attended),
        ];

        $data = Pipeline::send($query)
            ->through($pipes)
            ->thenReturn();

        return RepositoryResponseDto::success()
            ->setData($data)
            ->setStatus(200)
            ->setMessage('Students retrieved successfully');
    }
}
