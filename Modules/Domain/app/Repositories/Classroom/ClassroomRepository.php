<?php

declare(strict_types=1);

namespace Modules\Domain\Repositories\Classroom;

use App\Repositories\BaseRepository\BaseRepository;
use Illuminate\Support\Facades\DB;
use Modules\Core\DTO\ResponseDto\RepositoryResponseDto;
use Modules\Domain\DTO\Student\CreateClassroomDto;
use Modules\Domain\Events\ClassroomCreated;
use Modules\Domain\Models\Classroom;
use Modules\Domain\Models\Student;

class ClassroomRepository extends BaseRepository implements ClassroomRepositoryInterface
{
    public function __construct(Classroom $model)
    {
        parent::__construct($model);
    }

    public function createClassroom(CreateClassroomDto $dto): RepositoryResponseDto
    {
        return DB::transaction(function () use ($dto) {
            /** @var Classroom $classroom */
            $classroom = $this->create([
                'teacher_id' => $dto->teacherId,
                'subject_id' => $dto->subjectId,
                'start_at' => $dto->startAt,
                'end_at' => $dto->endAt,
                'lat' => $dto->lat,
                'lng' => $dto->lng,
                'radius' => $dto->radius,
            ]);

            $msg = 'Classroom created successfully';

            if (! empty($dto->studentsIds)) {
                $usersCount = Student::query()->whereIn('id', $dto->studentsIds)->count();

                if ($usersCount != \count($dto->studentsIds)) {
                    return RepositoryResponseDto::error('One or more students not found');
                }

                $classroom->students()->syncWithoutDetaching($dto->studentsIds);

                DB::afterCommit(fn () => event(new ClassroomCreated($classroom, $dto->studentsIds)));

                $msg = 'Classroom created successfully and students synced with email notifications';
            }

            return RepositoryResponseDto::success()
                ->setMessage($msg)
                ->setData($classroom)
                ->setStatus(201);
        });
    }
}
