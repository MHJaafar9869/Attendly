<?php

declare(strict_types=1);

namespace Modules\Core\Http\Controllers\Api\Role;

use Modules\Core\Repositories\Role\RoleRepositoryInterface;
use App\Traits\ResponseJson;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Modules\Core\Http\Requests\Role\StoreRoleRequest;
use Modules\Core\Http\Requests\Role\UpdateRoleRequest;
use Modules\Core\Transformers\Role\RoleResource;

class RoleController extends Controller
{
    use ResponseJson;

    public function __construct(
        protected RoleRepositoryInterface $roleRepo
    ) {
        //
    }

    public function index(): JsonResponse
    {
        $data = $this->roleRepo->all();
        return $this->respondWithData(RoleResource::collection($data), 'Role list retrieved successfully');
    }

    public function show(int|string $id): JsonResponse
    {
        $data = $this->roleRepo->find($id);
        if (!$data) {
            return $this->respondError('Role not found', 404);
        }
        return $this->respondWithData(RoleResource::make($data), 'Role retrieved successfully');
    }

    public function store(StoreRoleRequest $request): JsonResponse
    {
        $data = $this->roleRepo->create($request->validated());
        return $this->respondWithData(RoleResource::make($data), 'Role created successfully', 201);
    }

    public function update(UpdateRoleRequest $request, int|string $id): JsonResponse
    {
        $data = $this->roleRepo->update($id, $request->validated());
        return $this->respondWithData(RoleResource::make($data), 'Role updated successfully');
    }

    public function destroy(int|string $id): JsonResponse
    {
        $this->roleRepo->delete($id);
        return $this->respondSuccess('Role deleted successfully');
    }


}
