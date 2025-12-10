<?php

declare(strict_types=1);

namespace Modules\Domain\Repositories\Classroom;

use App\Repositories\BaseRepository\BaseRepository;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Modules\Core\DTO\ResponseDto\RepositoryResponseDto;
use Modules\Core\Pipelines\DateRangeFilter;
use Modules\Core\Pipelines\OrderByFilter;
use Modules\Core\Pipelines\RelationFilter;
use Modules\Domain\DTO\Classroom\ClassroomFilterDto;
use Modules\Domain\DTO\Student\CreateClassroomDto;
use Modules\Domain\DTO\Student\UpdateClassroomDto;
use Modules\Domain\Events\ClassroomCreatedEvent;
use Modules\Domain\Events\ClassroomUpdatedEvent;
use Modules\Domain\Models\Classroom;
use Modules\Domain\Models\Student;

readonly class ClassroomRepository extends BaseRepository implements ClassroomRepositoryInterface
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

    public function searchClassrooms(ClassroomFilterDto $dto): RepositoryResponseDto
    {
        $key = hash('sha1', json_encode($dto->toArray()));

        $query = $this->allWithRelations(
            [
                'teacher:id,user_id',
                'subject:id,name',
                'students:id,student_code',
                'students.user:id,first_name',
            ]
        );

        $pipes = [
            new RelationFilter('teacher', $dto->teacher, 'teacher_code'),
            new RelationFilter('subject', $dto->subject, 'name'),
            new DateRangeFilter('start_at', $dto->startMin, $dto->startMax),
            new DateRangeFilter('end_at', $dto->endMin, $dto->endMax),
            new OrderByFilter($dto->orderBy, $dto->orderDir),
        ];

        Pipeline::send($query)
            ->through($pipes)
            ->thenReturn();

        $cachecd = Cache::flexible(
            key: "classroom:{$key}:{$dto->page}",
            ttl: [10, 30],
            callback: fn () => Pipeline::send($query)
                ->through($pipes)
                ->thenReturn()
                ->paginate(15, ['*'], 'classrooms', $dto->page)
        );

        return RepositoryResponseDto::success()
            ->setData($cachecd)
            ->setStatus(200)
            ->setMessage('Classrooms retrieved successfully');
    }
}
