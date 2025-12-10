<?php

declare(strict_types=1);

namespace Modules\Domain\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Modules\Core\DTO\Elequent\PaginateDto;
use Modules\Core\Traits\ResponseJson;
use Modules\Domain\DTO\Student\CreateClassroomDto;
use Modules\Domain\DTO\Student\UpdateClassroomDto;
use Modules\Domain\Http\Requests\Classroom\StoreClassroomRequest;
use Modules\Domain\Http\Requests\Classroom\UpdateClassroomRequest;
use Modules\Domain\Repositories\Classroom\ClassroomRepositoryInterface;
use Modules\Domain\Services\ClassroomService;
use Modules\Domain\Transformers\Classroom\ClassroomResource;

class ClassroomController extends Controller
{
    use ResponseJson;

    public function __construct(
        protected ClassroomRepositoryInterface $classroomRepo,
        protected ClassroomService $classroomService
    ) {
        //
    }

    public function index(Request $request): JsonResponse
    {
        $dto = PaginateDto::fromRequest($request->all());

        $data = Cache::flexible('classrooms', [30, 60], fn () => $this->classroomRepo->paginate($dto));

        return $this->respondWithData(ClassroomResource::collection($data), 'Classroom list retrieved successfully');
    }

    public function show(int|string $id): JsonResponse
    {
        $data = $this->classroomService->getClass($id);

        return $this->respondWithData(ClassroomResource::make($data), 'Classroom retrieved successfully');
    }

    public function store(StoreClassroomRequest $request): JsonResponse
    {
        $dto = CreateClassroomDto::fromRequest($request->validated());

        $response = $this->classroomService->create($dto);

        return $this->respondDto($response);
    }

    public function update(UpdateClassroomRequest $request, int|string $id): JsonResponse
    {
        $data = UpdateClassroomDto::fromRequest($request->validated(), $id);

        $response = $this->classroomService->update($data);

        return $this->respondDto($response);
    }

    public function destroy(int|string $id): JsonResponse
    {
        $this->classroomRepo->delete($id);

        return $this->respondSuccess('Classroom deleted successfully');
    }
}
