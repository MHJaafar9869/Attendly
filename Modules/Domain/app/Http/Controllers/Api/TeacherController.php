<?php

declare(strict_types=1);

namespace Modules\Domain\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Core\Traits\ResponseJson;
use Modules\Domain\Http\Requests\Teacher\StoreTeacherRequest;
use Modules\Domain\Http\Requests\Teacher\UpdateTeacherRequest;
use Modules\Domain\Repositories\Teacher\TeacherRepositoryInterface;
use Modules\Domain\Transformers\Teacher\TeacherResource;

class TeacherController extends Controller
{
    use ResponseJson;

    public function __construct(
        protected TeacherRepositoryInterface $teacherRepo
    ) {
        //
    }

    public function index(): JsonResponse
    {
        $data = $this->teacherRepo->all();

        return $this->respondWithData(TeacherResource::collection($data), 'Teacher list retrieved successfully');
    }

    public function show(int | string $id): JsonResponse
    {
        $data = $this->teacherRepo->find($id);
        if (! $data) {
            return $this->respondError('Teacher not found', 404);
        }

        return $this->respondWithData(TeacherResource::make($data), 'Teacher retrieved successfully');
    }

    public function store(StoreTeacherRequest $request): JsonResponse
    {
        $data = $this->teacherRepo->create($request->validated());

        return $this->respondWithData(TeacherResource::make($data), 'Teacher created successfully', 201);
    }

    public function update(UpdateTeacherRequest $request, int | string $id): JsonResponse
    {
        $data = $this->teacherRepo->update($id, $request->validated());

        return $this->respondWithData(TeacherResource::make($data), 'Teacher updated successfully');
    }

    public function destroy(int | string $id): JsonResponse
    {
        $this->teacherRepo->delete($id);

        return $this->respondSuccess('Teacher deleted successfully');
    }
}
