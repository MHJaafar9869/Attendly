<?php

declare(strict_types=1);

namespace Modules\Domain\Repositories\Student;

use App\Repositories\BaseRepository\BaseRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Core\DTO\Elequent\PaginateDto;
use Modules\Core\Models\User;
use Modules\Domain\Models\Student;

final readonly class StudentRepository extends BaseRepository implements StudentRepositoryInterface
{
    public function __construct(Student $model, protected User $user)
    {
        parent::__construct($model);
    }

    public function paginatedStudents(PaginateDto $dto): LengthAwarePaginator
    {
        $relations = [
            'user',
            'governorate',
            'classrooms',
        ];

        return $this->paginateWithRelations($dto, $relations);
    }
}
