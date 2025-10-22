<?php

declare(strict_types=1);

namespace Modules\Domain\Repositories\Student;

use Modules\Domain\Repositories\Student\StudentRepositoryInterface;
use App\Repositories\BaseRepository\BaseRepository;
use Modules\Domain\Models\Student;

class StudentRepository extends BaseRepository implements StudentRepositoryInterface
{
    public function __construct(Student $model)
    {
        parent::__construct($model);
    }
}
