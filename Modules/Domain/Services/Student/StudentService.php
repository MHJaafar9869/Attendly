<?php

declare(strict_types=1);

namespace Modules\Domain\Services\Student;

use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Pipeline;
use Modules\Core\DTO\Elequent\PaginateDto;
use Modules\Core\DTO\ResponseDto\ServiceResponseDto;
use Modules\Core\Pipelines\BooleanFilter;
use Modules\Core\Pipelines\RelationFilter;
use Modules\Core\Pipelines\SearchFilter;
use Modules\Core\Services\BaseService;
use Modules\Domain\DTO\Student\StudentFilterDto;
use Modules\Domain\Models\Student;
use Modules\Domain\Repositories\Student\StudentRepositoryInterface;
use Modules\Domain\Transformers\Student\StudentResource;

final readonly class StudentService extends BaseService
{
    public function __construct(
        protected readonly StudentRepositoryInterface $studentRepo
    ) {}

    public function getStudents(PaginateDto $dto, string $key): ServiceResponseDto
    {
        $response = Cache::flexible(
            key: "students:{$key}",
            ttl: [30, 60],
            callback: fn () => $this->studentRepo->paginateWithRelations($dto, [
                'user:id,fullname',
                'governorate:id,name,iso_code',
            ])
        );

        return ServiceResponseDto::success()
            ->setMessage('Students list retrieved successfully')
            ->setData(StudentResource::collection($response))
            ->setStatus(200);
    }

    public function getStudent(string $id): ServiceResponseDto
    {
        $student = Cache::flexible(
            key: "students:{$id}",
            ttl: [30, 60],
            callback: fn () => $this->studentRepo->findWithRelations($id, [
                'user:id,fullname',
                'governorate:id,name,iso_code',
                'classrooms:id,attended',
                'classrooms.subject:id,name',
                'classrooms.teacher.user:id,fullname',
            ]),
        );

        if (! $student) {
            return ServiceResponseDto::error("Invalid ID: {$id}", 404);
        }

        return ServiceResponseDto::success()
            ->setMessage('Student retrieved successfully')
            ->setData(StudentResource::make($student))
            ->setStatus(200);
    }

    public function searchStudents(StudentFilterDto $dto, string $key): ServiceResponseDto
    {
        $query = $this->studentRepo->allWithRelations(
            [
                'user',
                'governorate',
                'classrooms',
            ]
        );

        $pipes = [
            new SearchFilter($dto->searchText, ['student_code', 'address', 'city']),
            new BooleanFilter('is_banned', $dto->isBanned),
            new RelationFilter($dto->gender, 'user', 'gender'),
            new BooleanFilter('attended', $dto->attended),
        ];

        $cachecd = Cache::flexible(
            key: "students:{$key}:{$dto->page}",
            ttl: [10, 30],
            callback: fn () => Pipeline::send($query)
                ->through($pipes)
                ->thenReturn()
                ->paginate(15, ['*'], 'students', $dto->page)
        );

        return ServiceResponseDto::success()
            ->setData($cachecd)
            ->setStatus(200)
            ->setMessage('Students retrieved successfully');
    }

    public function getStudentsAnalytics(): ServiceResponseDto
    {
        try {
            $analytics = Cache::flexible(
                key: 'students:analytics',
                ttl: [300, 600], // 5 - 10 minutes
                callback: function () {
                    $total = $this->studentRepo->addQuery()->count();

                    $avgRow = DB::selectOne(
                        'SELECT AVG(att_avg) as avg_attendance
                        FROM (SELECT AVG(CAST(attended AS UNSIGNED)) as att_avg
                        FROM student_classrooms GROUP BY student_id) t'
                    );

                    $avgAttendance = $avgRow->avg_attendance !== null ? (float) $avgRow->avg_attendance : 0;

                    $banned = Student::where('is_banned', true)->count();

                    $byGender = DB::table('students')
                        ->leftJoin('users', 'students.user_id', '=', 'users.id')
                        ->select(
                            // COALESCE returns the first non-null value; here it normalizes NULL genders to 'unknown'
                            DB::raw("COALESCE(users.gender, 'unknown') as gender"),
                            DB::raw('COUNT(students.id) as cnt')
                        )
                        ->groupBy('gender')
                        ->pluck('cnt', 'gender')
                        ->map(fn ($v) => (int) $v)
                        ->toArray();

                    $byCity = DB::table('students')
                        ->select(
                            // COALESCE normalizes NULL/empty city values to 'unknown' so grouping includes them
                            DB::raw("COALESCE(city, 'unknown') as city"),
                            DB::raw('COUNT(id) as cnt')
                        )
                        ->groupBy('city')
                        ->pluck('cnt', 'city')
                        ->map(fn ($v) => (int) $v)
                        ->toArray();

                    $byGovernorate = DB::table('students')
                        ->leftJoin('governorates', 'students.governorate_id', '=', 'governorates.id')
                        ->select(
                            // COALESCE ensures governorates with NULL names are grouped under 'unknown'
                            DB::raw("COALESCE(governorates.name, 'unknown') as governorate"),
                            DB::raw('COUNT(students.id) as cnt')
                        )
                        ->groupBy('governorate')
                        ->pluck('cnt', 'governorate')
                        ->map(fn ($v) => (int) $v)
                        ->toArray();

                    return [
                        'total' => $total,
                        'avg_attendance' => $avgAttendance,
                        'banned' => $banned,
                        'by_gender' => $byGender,
                        'by_city' => $byCity,
                        'by_governorate' => $byGovernorate,
                    ];
                }
            );

            return ServiceResponseDto::success()
                ->setMessage('Students analytics retrieved successfully')
                ->setData($analytics)
                ->setStatus(200);
        } catch (Exception $e) {
            return $this->getErrorResponse($e);
        }
    }
}
