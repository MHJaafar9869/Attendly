<?php

declare(strict_types=1);

namespace Modules\Core\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Core\Http\Requests\Permission\StorePermissionRequest;
use Modules\Core\Http\Requests\Permission\UpdatePermissionRequest;
use Modules\Core\Repositories\Permission\PermissionRepositoryInterface;
use Modules\Core\Traits\ResponseJson;
use Modules\Core\Transformers\Permission\PermissionResource;

class PermissionController extends Controller
{
    use ResponseJson;

    public function __construct(
        protected PermissionRepositoryInterface $permissionRepo
    ) {
        //
    }

    public function index(): JsonResponse
    {
        $data = $this->permissionRepo->all();

        return $this->respondWithData(PermissionResource::collection($data), 'Permission list retrieved successfully');
    }

    public function show(int | string $id): JsonResponse
    {
        $data = $this->permissionRepo->find($id);

        if (! $data) {
            return $this->respondError('Permission not found', 404);
        }

        return $this->respondWithData(PermissionResource::make($data), 'Permission retrieved successfully');
    }

    public function store(StorePermissionRequest $request): JsonResponse
    {
        $data = $this->permissionRepo->create($request->validated());

        return $this->respondWithData(PermissionResource::make($data), 'Permission created successfully', 201);
    }

    public function update(UpdatePermissionRequest $request, int | string $id): JsonResponse
    {
        $data = $this->permissionRepo->update($id, $request->validated());

        return $this->respondWithData(PermissionResource::make($data), 'Permission updated successfully');
    }

    public function destroy(int | string $id): JsonResponse
    {
        $this->permissionRepo->delete($id);

        return $this->respondSuccess('Permission deleted successfully');
    }
}
