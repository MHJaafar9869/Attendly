<?php

declare(strict_types=1);

namespace Modules\Domain\Http\Controllers\Api\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Core\DTO\Elequent\PaginateDto;
use Modules\Core\Traits\ResponseJson;
use Modules\Domain\DTO\Teacher\TeacherFilterDto;
use Modules\Domain\Http\Requests\Teacher\StoreTeacherRequest;
use Modules\Domain\Http\Requests\Teacher\UpdateTeacherRequest;
use Modules\Domain\Repositories\Teacher\TeacherRepositoryInterface;
use Modules\Domain\Services\Teacher\TeacherService;
use Modules\Domain\Transformers\Teacher\TeacherResource;

final class TeacherController extends Controller
{
    use ResponseJson;

    public function __construct(
        protected readonly TeacherRepositoryInterface $teacherRepo,
        protected readonly TeacherService $teacherService
    ) {}

    /**
     * List all teachers.
     * GET /api/v1/teachers
     */
    public function index(Request $request): JsonResponse
    {
        $dto = PaginateDto::fromRequest($request->only(['per_page', 'page', 'page_name', 'columns']));
        $key = hash('sha1', json_encode($request->all()));

        $response = $this->teacherService->getTeachers($dto, $key);

        return $this->respondWithPagination($response->data, $response->message);
    }

    /**
     * Show a specific teacher.
     * GET /api/v1/teachers/{id}
     */
    public function show(string $id): JsonResponse
    {
        $response = $this->teacherService->getTeacher($id);

        return $this->respondDto($response);
    }

    /**
     * Store a new teacher.
     * POST /api/v1/teachers
     */
    public function store(StoreTeacherRequest $request): JsonResponse
    {
        $data = $this->teacherRepo->create($request->validated());

        return $this->respondWithData(TeacherResource::make($data), 'Teacher created successfully', 201);
    }

    /**
     * Update an existing teacher.
     * PUT /api/v1/teachers/{id}
     */
    public function update(UpdateTeacherRequest $request, string $id): JsonResponse
    {
        $data = $this->teacherRepo->update($id, $request->validated());

        return $this->respondWithData(TeacherResource::make($data), 'Teacher updated successfully');
    }

    /**
     * Delete a teacher.
     * DELETE /api/v1/teachers/{id}
     */
    public function destroy(string $id): JsonResponse
    {
        $this->teacherRepo->delete($id);

        return $this->respondSuccess('Teacher deleted successfully');
    }

    /**
     * Restore a deleted teacher.
     * PATCH /api/v1/teachers/{id}
     */
    public function restore(string $id): JsonResponse
    {
        $this->teacherRepo->restore($id);

        return $this->respondSuccess('Teacher restored successfully');
    }

    /**
     * Delete a teacher permanently.
     * DELETE /api/v1/teachers/{id}/force
     */
    public function forceDelete(string $id): JsonResponse
    {
        $this->teacherRepo->forceDelete($id);

        return $this->respondSuccess('Teacher permanently deleted successfully');
    }

    /**
     * Search teachers.
     * GET /api/v1/teachers/search
     */
    public function searchTeachers(Request $request): JsonResponse
    {
        $dto = TeacherFilterDto::fromRequest($request->all());
        $key = hash('sha1', json_encode($request->all()));

        $response = $this->teacherService->searchTeachers($dto, $key);

        return $this->respondDto($response);
    }

    public function analytics(): JsonResponse
    {
        $response = $this->teacherService->getTeachersAnalytics();

        return $this->respondDto($response);
    }
}
