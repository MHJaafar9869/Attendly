<?php

declare(strict_types=1);

namespace Modules\Domain\Repositories\Student;

use App\Repositories\BaseRepository\BaseRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Core\DTO\Elequent\PaginateDto;

interface StudentRepositoryInterface extends BaseRepositoryInterface
{
    public function paginatedStudents(PaginateDto $dto): LengthAwarePaginator;
}
