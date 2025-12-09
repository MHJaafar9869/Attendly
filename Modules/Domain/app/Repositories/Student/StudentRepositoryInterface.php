<?php

declare(strict_types=1);

namespace Modules\Domain\Repositories\Student;

use App\Repositories\BaseRepository\BaseRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Core\DTO\ResponseDto\RepositoryResponseDto;
use Modules\Domain\DTO\Student\StudentFilterDto;

interface StudentRepositoryInterface extends BaseRepositoryInterface
{
    public function paginatedStudents(int $perPage = 15, string $pageName = 'students'): LengthAwarePaginator;

    public function searchStudents(StudentFilterDto $dto): RepositoryResponseDto;
}
