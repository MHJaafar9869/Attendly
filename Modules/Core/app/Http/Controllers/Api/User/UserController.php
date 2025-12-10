<?php

declare(strict_types=1);

namespace Modules\Core\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Core\Http\Requests\User\StoreUserRequest;
use Modules\Core\Http\Requests\User\UpdateUserRequest;
use Modules\Core\Repositories\Image\ImageRepositoryInterface;
use Modules\Core\Repositories\User\UserRepositoryInterface;
use Modules\Core\Traits\ResponseJson;
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

    public function index(Request $request): JsonResponse
    {
        $data = $this->userRepo->paginateWithRelations($request->integer('per_page', 15), 'users');

        return $this->respondWithData(
            UserResource::collection($data),
            'User list retrieved successfully'
        );
    }

    public function show(int|string $id): JsonResponse
    {
        $data = $this->userRepo->findWithRelations($id, [
            'roles:id,name',
            'status:id,name,text_color,bg_color',
            'contacts:id,type_id,value,is_active,order',
            'images:id,image_path,image_url,image_alt,type',
        ]);

        if (! $data instanceof Model) {
            return $this->respondError('User not found', 404);
        }

        return $this->respondWithData(
            UserResource::make($data),
            'User retrieved successfully'
        );
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

    public function searchUsers()
    {
        // ...
    }
}
