<?php

declare(strict_types=1);

namespace Modules\Domain\Repositories\Classroom;

use App\Repositories\BaseRepository\BaseRepository;
use Illuminate\Support\Facades\DB;
use Modules\Core\DTO\ResponseDto\RepositoryResponseDto;
use Modules\Domain\DTO\Student\CreateClassroomDto;
use Modules\Domain\DTO\Student\UpdateClassroomDto;
use Modules\Domain\Events\ClassroomCreatedEvent;
use Modules\Domain\Events\ClassroomUpdatedEvent;
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

            if ($dto->studentsIds !== []) {
                $usersCount = Student::query()->whereIn('id', $dto->studentsIds)->count();

                if ($usersCount != \count($dto->studentsIds)) {
                    return RepositoryResponseDto::error('One or more students not found');
                }

                $classroom->students()->syncWithoutDetaching($dto->studentsIds);

                DB::afterCommit(fn () => event(new ClassroomCreatedEvent($classroom->id, $dto->studentsIds)));

                $msg = 'Classroom created successfully and students synced with email notifications';
            }

            return RepositoryResponseDto::success()
                ->setMessage($msg)
                ->setData($classroom)
                ->setStatus(201);
        });
    } // End Method: createClassroom

    public function updateClassroom(UpdateClassroomDto $dto): RepositoryResponseDto
    {
        return DB::transaction(function () use ($dto) {
            /** @var Classroom|null $classroom */
            if (! $classroom = $this->findWithRelations($dto->id, 'students')) {
                return RepositoryResponseDto::error()
                    ->setMessage("Classroom not found for ID: {$dto->id}")
                    ->setStatus(404);
            }

            $updateData = collect([
                'teacher_id' => $dto->teacherId,
                'subject_id' => $dto->subjectId,
                'start_at' => $dto->startAt,
                'end_at' => $dto->endAt,
                'lat' => $dto->lat,
                'lng' => $dto->lng,
                'radius' => $dto->radius,
            ])->filter()->toArray();

            if (! empty($updateData)) {
                $classroom->update($updateData);
            }

            $msg = 'Classroom updated successfully';

            if ($dto->studentsIds !== null) {
                if (empty($dto->studentsIds)) {
                    $classroom->students()->sync([]);
                    $msg = 'Classroom updated successfully and all students were removed.';
                } else {
                    $usersCount = Student::query()->whereIn('id', $dto->studentsIds)->count();

                    if ($usersCount != \count($dto->studentsIds)) {
                        return RepositoryResponseDto::error('One or more students not found.');
                    }

                    $classroom->students()->sync($dto->studentsIds);
                    $msg = 'Classroom updated successfully and students synchronized.';
                }

                DB::afterCommit(fn () => event(new ClassroomUpdatedEvent($classroom->id, $dto->studentsIds)));
            } else {
                DB::afterCommit(fn () => event(new ClassroomUpdatedEvent($classroom->id, $classroom->students()->pluck('id')->toArray())));
            }

            return RepositoryResponseDto::success()
                ->setMessage($msg)
                ->setData($classroom)
                ->setStatus(200);
        });
    } // End Method: updateClassroom
}
