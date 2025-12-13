<?php

declare(strict_types=1);

namespace Modules\Domain\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Core\DTO\Elequent\PaginateDto;
use Modules\Core\Traits\ResponseJson;
use Modules\Domain\DTO\Student\StudentFilterDto;
use Modules\Domain\Http\Requests\Student\StoreStudentRequest;
use Modules\Domain\Http\Requests\Student\UpdateStudentRequest;
use Modules\Domain\Repositories\Student\StudentRepositoryInterface;
use Modules\Domain\Services\Student\StudentService;
use Modules\Domain\Transformers\Student\StudentResource;

class StudentController extends Controller
{
    use ResponseJson;

    public function __construct(
        protected StudentRepositoryInterface $studentRepo,
        protected readonly StudentService $studentService
    ) {
        //
    }

    /**
     * List all students.
     * GET /api/v1/students
     */
    public function index(Request $request): JsonResponse
    {
        $dto = PaginateDto::fromRequest($request->only(['per_page', 'page', 'page_name', 'columns']));
        $key = hash('sha1', json_encode($request->all()));

        $response = $this->studentService->getStudents($dto, $key);

        return $this->respondWithPagination(
            $response->data,
            $response->message,
        );
    }

    /**
     * Show a specific student.
     * GET /api/v1/students/{id}
     */
    public function show(string $id): JsonResponse
    {
        $response = $this->studentService->getStudent($id);

        return $this->respondDto($response);
    }

    /**
     * Store a new student.
     * POST /api/v1/students
     */
    public function store(StoreStudentRequest $request): JsonResponse
    {
        $data = $this->studentRepo->create($request->validated());

        return $this->respondWithData(StudentResource::make($data), 'Student created successfully', 201);
    }

    /**
     * Update an existing student.
     * PUT /api/v1/students/{id}
     */
    public function update(UpdateStudentRequest $request, string $id): JsonResponse
    {
        $data = $this->studentRepo->update($id, $request->validated());

        return $this->respondWithData(StudentResource::make($data), 'Student updated successfully');
    }

    /**
     * Delete a student.
     * DELETE /api/v1/students/{id}
     */
    public function destroy(string $id): JsonResponse
    {
        $this->studentRepo->delete($id);

        return $this->respondSuccess('Student deleted successfully');
    }

    /**
     * Restore a deleted student.
     * PATCH /api/v1/students/{id}
     */
    public function restore(string $id): JsonResponse
    {
        $this->studentRepo->restore($id);

        return $this->respondSuccess('Student restored successfully');
    }

    /**
     * Delete a student permanently.
     * DELETE /api/v1/students/{id}/force
     */
    public function forceDelete(string $id): JsonResponse
    {
        $this->studentRepo->forceDelete($id);

        return $this->respondSuccess('Student deleted permanently');
    }

    /**
     * Search students.
     * GET /api/v1/students/search
     */
    public function searchStudents(Request $request): JsonResponse
    {
        $dto = StudentFilterDto::fromRequest($request->all());
        $key = hash('sha1', json_encode($request->all()));

        $response = $this->studentService->searchStudents($dto, $key);

        return $this->respondDto($response);
    }
}
