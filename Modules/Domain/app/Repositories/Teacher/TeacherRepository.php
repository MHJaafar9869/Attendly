<?php

declare(strict_types=1);

namespace Modules\Domain\Repositories\Teacher;

use Modules\Domain\Repositories\Teacher\TeacherRepositoryInterface;
use App\Repositories\BaseRepository\BaseRepository;
use Modules\Domain\Models\Teacher;

class TeacherRepository extends BaseRepository implements TeacherRepositoryInterface
{
    public function __construct(Teacher $model)
    {
        parent::__construct($model);
    }
}
