<?php

declare(strict_types=1);

namespace Modules\Domain\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Core\Traits\ResponseJson;
use Modules\Domain\DTO\Student\StudentFilterDto;
use Modules\Domain\Http\Requests\Student\StoreStudentRequest;
use Modules\Domain\Http\Requests\Student\UpdateStudentRequest;
use Modules\Domain\Repositories\Student\StudentRepositoryInterface;
use Modules\Domain\Transformers\Student\StudentResource;

class StudentController extends Controller
{
    use ResponseJson;

    public function __construct(
        protected StudentRepositoryInterface $studentRepo
    ) {
        //
    }

    /**
     * List all students.
     * GET /api/v1/students
     */
    public function index(): JsonResponse
    {
        return $this->respondWithData(
            data: StudentResource::collection($this->studentRepo->paginatedStudents()),
            message: 'Student list retrieved successfully');
    }

    /**
     * Show a specific student.
     * GET /api/v1/students/{id}
     */
    public function show(int|string $id): JsonResponse
    {
        $data = $this->studentRepo->findWithRelations(
            id: $id,
            relations: [
                'user',
                'governorate',
                'classrooms',
            ]
        );

        if (! $data) {
            return $this->respondError('Student not found', 404);
        }

        return $this->respondWithData(StudentResource::make($data), 'Student retrieved successfully');
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
    public function update(UpdateStudentRequest $request, int|string $id): JsonResponse
    {
        $data = $this->studentRepo->update($id, $request->validated());

        return $this->respondWithData(StudentResource::make($data), 'Student updated successfully');
    }

    /**
     * Delete a student.
     * DELETE /api/v1/students/{id}
     */
    public function destroy(int|string $id): JsonResponse
    {
        $this->studentRepo->delete($id);

        return $this->respondSuccess('Student deleted successfully');
    }

    /**
     * Restore a deleted student.
     * PATCH /api/v1/students/{id}
     */
    public function restore(int|string $id): JsonResponse
    {
        $this->studentRepo->restore($id);

        return $this->respondSuccess('Student restored successfully');
    }

    /**
     * Delete a student permanently.
     * DELETE /api/v1/students/{id}/force
     */
    public function forceDelete(int|string $id): JsonResponse
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

        $response = $this->studentRepo->searchStudents($dto);

        return $this->respondDto($response);
    }
}
