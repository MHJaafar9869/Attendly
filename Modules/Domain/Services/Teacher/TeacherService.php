<?php

declare(strict_types=1);

namespace Modules\Domain\Services\Teacher;

use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Pipeline;
use Modules\Core\DTO\Elequent\PaginateDto;
use Modules\Core\DTO\ResponseDto\ServiceResponseDto;
use Modules\Core\Pipelines\OrderByFilter;
use Modules\Core\Pipelines\RelationFilter;
use Modules\Core\Pipelines\SearchFilter;
use Modules\Core\Services\BaseService;
use Modules\Domain\DTO\Teacher\TeacherFilterDto;
use Modules\Domain\Models\Classroom;
use Modules\Domain\Models\Teacher;
use Modules\Domain\Repositories\Teacher\TeacherRepositoryInterface;
use Modules\Domain\Transformers\Teacher\TeacherResource;

final readonly class TeacherService extends BaseService
{
    public function __construct(
        protected TeacherRepositoryInterface $teacherRepo
    ) {}

    public function getTeachers(PaginateDto $dto, string $key): ServiceResponseDto
    {
        $response = Cache::flexible(
            key: "teachers:{$key}",
            ttl: [30, 60],
            callback: fn () => $this->teacherRepo->paginateWithRelations($dto, [
                'user:id,fullname',
                'status:id,name',
                'department:id,name',
            ])
        );

        return ServiceResponseDto::success()
            ->setMessage('Teachers list retrieved successfully')
            ->setData(TeacherResource::collection($response))
            ->setStatus(200);
    }

    public function getTeacher(string $id): ServiceResponseDto
    {
        $teacher = Cache::flexible(
            key: "teachers:{$id}",
            ttl: [30, 60],
            callback: fn () => $this->teacherRepo->findWithRelations($id, [
                'user:id,fullname',
                'status:id,name',
                'department:id,name',
            ]),
        );

        if (! $teacher) {
            return ServiceResponseDto::error("Invalid ID: {$id}", 404);
        }

        return ServiceResponseDto::success()
            ->setMessage('Teacher retrieved successfully')
            ->setData(TeacherResource::make($teacher))
            ->setStatus(200);
    }

    public function searchTeachers(TeacherFilterDto $dto, string $key): ServiceResponseDto
    {
        $query = $this->teacherRepo->allWithRelations(
            [
                'user:id,fullname',
                'status:id,name,text_color,bg_color',
                'department:id,name',
            ]
        );

        $pipes = [
            new RelationFilter($dto->gender, 'user', 'gender'),
            new RelationFilter($dto->status, 'status', 'name'),
            new RelationFilter($dto->department, 'department', 'name'),
            new SearchFilter($dto->searchText, 'teacher_code'),
            new OrderByFilter($dto->orderBy, $dto->orderDir),
        ];

        $cachecd = Cache::flexible(
            key: "teachers:{$key}:{$dto->page}",
            ttl: [10, 30],
            callback: fn () => Pipeline::send($query)
                ->through($pipes)
                ->thenReturn()
                ->paginate(15, ['*'], 'teachers', $dto->page)
        );

        return ServiceResponseDto::success()
            ->setData($cachecd)
            ->setStatus(200)
            ->setMessage('Students retrieved successfully');
    }

    public function getTeachersAnalytics(): ServiceResponseDto
    {
        try {
            $analytics = Cache::flexible(
                key: 'teachers:analytics',
                ttl: [300, 600], // 5 - 10 minutes
                callback: function () {
                    $total = Teacher::count();

                    $totalClassrooms = Classroom::count();

                    $avgClassroomsPerTeacher = $total === 0 ? 0 : round($totalClassrooms / $total, 2);

                    $now = now();
                    $activeClassrooms = Classroom::query()
                        ->where('start_at', '<=', $now)
                        ->where('end_at', '>=', $now)
                        ->count();

                    $uniqueStudents = DB::table('student_classrooms')
                        ->join('classrooms', 'student_classrooms.classroom_id', '=', 'classrooms.id')
                        ->distinct()
                        ->count('student_classrooms.student_id');

                    // COALESCE returns the first non-null value; normalize NULL genders to 'unknown'
                    $byGender = DB::table('teachers')
                        ->leftJoin('users', 'teachers.user_id', '=', 'users.id')
                        ->select(DB::raw("COALESCE(users.gender, 'unknown') as gender"), DB::raw('COUNT(teachers.id) as cnt'))
                        ->groupBy('gender')
                        ->pluck('cnt', 'gender')
                        ->map(fn ($v) => (int) $v)
                        ->toArray();

                    // COALESCE groups NULL department names under 'unknown'
                    $byDepartment = DB::table('teacher_departments')
                        ->join('departments', 'teacher_departments.department_id', '=', 'departments.id')
                        ->select(DB::raw("COALESCE(departments.name, 'unknown') as department"), DB::raw('COUNT(DISTINCT teacher_departments.teacher_id) as cnt'))
                        ->groupBy('department')
                        ->pluck('cnt', 'department')
                        ->map(fn ($v) => (int) $v)
                        ->toArray();

                    // COALESCE ensures null statuses are reported as 'unknown'
                    $byStatus = DB::table('teachers')
                        ->leftJoin('statuses', 'teachers.status_id', '=', 'statuses.id')
                        ->select(DB::raw("COALESCE(statuses.name, 'unknown') as status"), DB::raw('COUNT(teachers.id) as cnt'))
                        ->groupBy('status')
                        ->pluck('cnt', 'status')
                        ->map(fn ($v) => (int) $v)
                        ->toArray();

                    return [
                        'total' => $total,
                        'total_classrooms' => $totalClassrooms,
                        'avg_classrooms_per_teacher' => $avgClassroomsPerTeacher,
                        'active_classrooms' => $activeClassrooms,
                        'unique_students_taught' => $uniqueStudents,
                        'by_gender' => $byGender,
                        'by_department' => $byDepartment,
                        'by_status' => $byStatus,
                    ];
                }
            );

            return ServiceResponseDto::success()
                ->setMessage('Teachers analytics retrieved successfully')
                ->setData($analytics)
                ->setStatus(200);
        } catch (Exception $e) {
            return $this->getErrorResponse($e);
        }
    }
}
