<?php

declare(strict_types=1);

namespace Modules\Core\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Traits\ResponseJson;
use Illuminate\Http\JsonResponse;
use Modules\Core\Http\Requests\User\StoreUserRequest;
use Modules\Core\Http\Requests\User\UpdateUserRequest;
use Modules\Core\Repositories\Image\ImageRepositoryInterface;
use Modules\Core\Repositories\User\UserRepositoryInterface;
use Modules\Core\Transformers\User\UserResource;

class UserController extends Controller
{
    use ResponseJson;

    public function __construct(
        protected UserRepositoryInterface $userRepo,
        protected ImageRepositoryInterface $imageRepo
    ) {
        //
    }

    public function index(): JsonResponse
    {
        $data = $this->userRepo->all();

        return $this->respondWithData(UserResource::collection($data), 'User list retrieved successfully');
    }

    public function show(int|string $id): JsonResponse
    {
        $data = $this->userRepo->find($id);

        if (! $data) {
            return $this->respondError('User not found', 404);
        }

        return $this->respondWithData(UserResource::make($data), 'User retrieved successfully');
    }

    public function store(StoreUserRequest $request): JsonResponse
    {
        $data = $this->userRepo->create($request->validated());

        return $this->respondWithData(UserResource::make($data), 'User created successfully', 201);
    }

    public function update(UpdateUserRequest $request, int|string $id): JsonResponse
    {
        $data = $this->userRepo->update($id, $request->validated());

        return $this->respondWithData(UserResource::make($data), 'User updated successfully');
    }

    public function destroy(int|string $id): JsonResponse
    {
        $this->userRepo->delete($id);

        return $this->respondSuccess('User deleted successfully');
    }
}
