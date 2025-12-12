<?php

declare(strict_types=1);

namespace Modules\Domain\Repositories\Classroom;

use App\Repositories\BaseRepository\BaseRepositoryInterface;
use Modules\Core\DTO\ResponseDto\RepositoryResponseDto;
use Modules\Domain\DTO\Classroom\CreateClassroomDto;
use Modules\Domain\DTO\Classroom\UpdateClassroomDto;

interface ClassroomRepositoryInterface extends BaseRepositoryInterface
{
    public function createClassroom(CreateClassroomDto $dto): RepositoryResponseDto;

    public function updateClassroom(UpdateClassroomDto $dto): RepositoryResponseDto;
}
