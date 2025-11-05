<?php

declare(strict_types=1);

namespace Modules\Domain\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ResponseJson;
use Illuminate\Http\JsonResponse;
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

    public function index(): JsonResponse
    {
        $data = $this->studentRepo->all();

        return $this->respondWithData(StudentResource::collection($data), 'Student list retrieved successfully');
    }

    public function show(int | string $id): JsonResponse
    {
        $data = $this->studentRepo->find($id);
        if (! $data) {
            return $this->respondError('Student not found', 404);
        }

        return $this->respondWithData(StudentResource::make($data), 'Student retrieved successfully');
    }

    public function store(StoreStudentRequest $request): JsonResponse
    {
        $data = $this->studentRepo->create($request->validated());

        return $this->respondWithData(StudentResource::make($data), 'Student created successfully', 201);
    }

    public function update(UpdateStudentRequest $request, int | string $id): JsonResponse
    {
        $data = $this->studentRepo->update($id, $request->validated());

        return $this->respondWithData(StudentResource::make($data), 'Student updated successfully');
    }

    public function destroy(int | string $id): JsonResponse
    {
        $this->studentRepo->delete($id);

        return $this->respondSuccess('Student deleted successfully');
    }
}
