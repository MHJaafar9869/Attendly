<?php

declare(strict_types=1);

namespace Modules\Domain\Repositories\Teacher;

use App\Repositories\BaseRepository\BaseRepository;
use Modules\Domain\Models\Teacher;

final readonly class TeacherRepository extends BaseRepository implements TeacherRepositoryInterface
{
    public function __construct(Teacher $model)
    {
        parent::__construct($model);
    }
}
