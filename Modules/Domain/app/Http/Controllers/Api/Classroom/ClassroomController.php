<?php

declare(strict_types=1);

namespace Modules\Domain\Http\Controllers\Api\Classroom;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Modules\Core\DTO\Elequent\PaginateDto;
use Modules\Core\Traits\ResponseJson;
use Modules\Domain\DTO\Classroom\ClassroomFilterDto;
use Modules\Domain\DTO\Classroom\CreateClassroomDto;
use Modules\Domain\DTO\Classroom\UpdateClassroomDto;
use Modules\Domain\Http\Requests\Classroom\StoreClassroomRequest;
use Modules\Domain\Http\Requests\Classroom\UpdateClassroomRequest;
use Modules\Domain\Repositories\Classroom\ClassroomRepositoryInterface;
use Modules\Domain\Services\Classroom\ClassroomService;
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

    // GET /api/v1/classrooms
    public function index(Request $request): JsonResponse
    {
        $dto = PaginateDto::fromRequest($request->all());

        $data = Cache::flexible('classrooms', [30, 60], fn () => $this->classroomRepo->paginate($dto));

        return $this->respondWithData(ClassroomResource::collection($data), 'Classroom list retrieved successfully');
    }

    // GET /api/v1/classrooms/{id}
    public function show(int | string $id): JsonResponse
    {
        $response = $this->classroomService->getClass($id);

        return $this->respondDto($response);
    }

    // POST /api/v1/classrooms
    public function store(StoreClassroomRequest $request): JsonResponse
    {
        $dto = CreateClassroomDto::fromRequest($request->validated());

        $response = $this->classroomService->create($dto);

        return $this->respondDto($response);
    }

    // PUT /api/v1/classrooms/{id}
    public function update(UpdateClassroomRequest $request, int | string $id): JsonResponse
    {
        $data = UpdateClassroomDto::fromRequest($request->validated(), $id);

        $response = $this->classroomService->update($data);

        return $this->respondDto($response);
    }

    // DELETE /api/v1/classrooms/{id}
    public function destroy(int | string $id): JsonResponse
    {
        $this->classroomRepo->delete($id);

        return $this->respondSuccess('Classroom deleted successfully');
    }

    // PATCH /api/v1/classrooms/{id}
    public function restore(int | string $id): JsonResponse
    {
        $this->classroomRepo->restore($id);

        return $this->respondSuccess('Classroom restored successfully');
    }

    // DELETE /api/v1/classrooms/{id}/force
    public function forceDelete(int | string $id): JsonResponse
    {
        $this->forceDelete($id);

        return $this->respondSuccess('Classroom deleted successfully');
    }

    // GET /api/v1/classrooms/search
    public function searchClassrooms(Request $request): JsonResponse
    {
        $dto = ClassroomFilterDto::fromRequest($request->all());
        $key = hash('sha1', json_encode($request->all()));

        $response = $this->classroomService->searchClassrooms($dto, $key);

        return $this->respondDto($response);
    }
}
