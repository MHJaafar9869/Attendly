<?php

declare(strict_types=1);

namespace Modules\Core\Services\UserServices;

use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Pipeline;
use Modules\Core\DTO\Elequent\PaginateDto;
use Modules\Core\DTO\ResponseDto\ServiceResponseDto;
use Modules\Core\DTO\UserFilterDto;
use Modules\Core\Pipelines\BooleanFilter;
use Modules\Core\Pipelines\DateRangeFilter;
use Modules\Core\Pipelines\OrderByFilter;
use Modules\Core\Pipelines\SearchFilter;
use Modules\Core\Repositories\User\UserRepositoryInterface;
use Modules\Core\Services\BaseService;
use Modules\Core\Transformers\User\UserResource;
use Modules\Domain\Models\Student;
use Modules\Domain\Models\Teacher;

final readonly class UserService extends BaseService
{
    public function __construct(
        protected UserRepositoryInterface $userRepo
    ) {}

    public function getUsers(PaginateDto $dto, string $key): ServiceResponseDto
    {
        $response = Cache::flexible(
            key: "users:{$key}",
            ttl: [30, 60],
            callback: fn () => $this->userRepo->paginateWithRelations($dto, [
                'roles:id,name',
                'status:id,name,text_color,bg_color',
                'images:id,image_path,image_url,image_alt,type',
            ])
        );

        return ServiceResponseDto::success()
            ->setMessage('User list retrieved successfully')
            ->setData(UserResource::collection($response))
            ->setStatus(200);
    }

    public function getUser(string $id): ServiceResponseDto
    {
        $data = Cache::flexible(
            key: "users:{$id}",
            ttl: [30, 60],
            callback: fn () => $this->userRepo->findWithRelations($id, [
                'roles:id,name',
                'status:id,name,text_color,bg_color',
                'contacts:id,type_id,value,is_active,order',
                'images:id,image_path,image_url,image_alt,type',
            ])
        );

        if (! $data) {
            return ServiceResponseDto::error('User not found', 404);
        }

        return ServiceResponseDto::success()
            ->setMessage('User retrieved successfully')
            ->setData(UserResource::make($data))
            ->setStatus(200);
    }

    public function searchUsers(UserFilterDto $dto): ServiceResponseDto
    {
        try {
            $query = $this->userRepo->allWithRelations(
                relations: [
                    'roles:id,name',
                    'status:id,name,text_color,bg_color',
                    'contacts:id,type_id,value,is_active,order',
                    'images:id,image_path,image_url,image_alt,type',
                ]
            );

            $pipes = [
                new SearchFilter($dto->search, ['first_name', 'last_name', 'email']),
                new BooleanFilter('is_logged_in', $dto->loggedIn),
                new BooleanFilter('email_verified_at', $dto->emailVerified),
                new OrderByFilter($dto->orderBy, $dto->orderDir),
                new DateRangeFilter($dto->dateRangeColumn, $dto->dateRangeStart, $dto->dateRangeEnd),
            ];

            $key = hash('sha1', json_encode($dto->toArray()));

            $response = Cache::flexible(
                key: "users:{$key}",
                ttl: [30, 60],
                callback: fn () => Pipeline::send($query)
                    ->through($pipes)
                    ->thenReturn()
                    ->get()
            );
        } catch (Exception $e) {
            return $this->getErrorResponse($e);
        }

        return ServiceResponseDto::success()
            ->setMessage('Users list retrieved successfully')
            ->setData(UserResource::collection($response))
            ->setStatus(200);
    }

    public function getUsersAnalytics(): ServiceResponseDto
    {
        try {
            $analytics = Cache::flexible(
                key: 'users:analytics',
                ttl: [300, 600], // 5 - 10 minutes
                callback: function () {
                    $query = $this->userRepo->allWithRelations(relations: [
                        'roles:id,name',
                        'status:id,name',
                        'contacts:id,user_id',
                        'images:id,imageable_id,imageable_type',
                    ]);

                    $total = (clone $query)->count();

                    $byRole = (clone $query)
                        ->get()
                        ->flatMap(fn ($u) => $u->roles->pluck('name'))
                        ->map(fn ($r) => $r ?? 'unknown')
                        ->countBy()
                        ->toArray();

                    $byStatus = (clone $query)
                        ->get()
                        ->map(fn ($u) => $u->status?->name ?? 'unknown')
                        ->countBy()
                        ->toArray();

                    $now = now();
                    $last7 = $this->userRepo->all()->whereBetween('created_at', [$now->copy()->subDays(7), $now])->count();
                    $last30 = $this->userRepo->all()->whereBetween('created_at', [$now->copy()->subDays(30), $now])->count();

                    $emailVerified = (clone $query)->whereNotNull('email_verified_at')->count();

                    $loggedIn = (clone $query)->where('is_logged_in', true)->count();

                    $twoFactor = (clone $query)
                        ->where(function ($q) {
                            $q->whereNotNull('two_factor_secret')
                                ->orWhereNotNull('two_factor_recovery_codes');
                        })->count();

                    $withImages = (clone $query)
                        ->get()
                        ->filter(fn ($u) => $u->images->isNotEmpty())
                        ->count();

                    $users = (clone $query)->get();
                    $avgContacts = $users->isEmpty() ? 0 : round($users->map(fn ($u) => $u->contacts->count())->avg(), 2);

                    $studentsCount = Student::count();
                    $teachersCount = Teacher::count();

                    return [
                        'total' => $total,
                        'by_role' => $byRole,
                        'by_status' => $byStatus,
                        'new' => [
                            'last_7_days' => $last7,
                            'last_30_days' => $last30,
                        ],
                        'email_verified' => $emailVerified,
                        'logged_in' => $loggedIn,
                        'two_factor_enabled' => $twoFactor,
                        'with_images' => $withImages,
                        'avg_contacts_per_user' => $avgContacts,
                        'students_count' => $studentsCount,
                        'teachers_count' => $teachersCount,
                    ];
                }
            );

            return ServiceResponseDto::success()
                ->setMessage('Users analytics retrieved successfully')
                ->setData($analytics)
                ->setStatus(200);
        } catch (Exception $e) {
            return $this->getErrorResponse($e);
        }
    }
}
