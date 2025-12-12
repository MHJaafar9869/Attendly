<?php

declare(strict_types=1);

namespace Modules\Core\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Core\DTO\Elequent\PaginateDto;
use Modules\Core\DTO\UserFilterDto;
use Modules\Core\Repositories\Image\ImageRepositoryInterface;
use Modules\Core\Repositories\User\UserRepositoryInterface;
use Modules\Core\Services\UserServices\UserService;
use Modules\Core\Traits\ResponseJson;

final class UserController extends Controller
{
    use ResponseJson;

    public function __construct(
        protected readonly UserService $userService,
        protected readonly UserRepositoryInterface $userRepo,
        protected readonly ImageRepositoryInterface $imageRepo,
    ) {
        //
    }

    public function index(Request $request): JsonResponse
    {
        $dto = PaginateDto::fromRequest($request->only(['per_page', 'page', 'page_name', 'columns']));
        $key = hash('sha1', json_encode($request->all()));

        $response = $this->userService->getUsers($dto, $key);

        return $this->respondWithPagination($response->data, $response->message);
    }

    public function show(string $id): JsonResponse
    {
        $response = $this->userService->getUser($id);

        return $this->respondDto($response);
    }

    public function searchUsers(Request $request): JsonResponse
    {
        $dto = UserFilterDto::fromRequest($request->all());

        $response = $this->userService->searchUsers($dto);

        return $this->respondDto($response);
    }

    public function destroy(int | string $id): JsonResponse
    {
        $this->userRepo->delete($id);

        return $this->respondSuccess('User deleted successfully');
    }

    public function restore(string $id): JsonResponse
    {
        $this->restore($id);

        return $this->respondSuccess('User restored successfully');
    }

    public function forceDelete(string $id): JsonResponse
    {
        $this->forceDelete($id);

        return $this->respondSuccess('User has been permenantly deleted');
    }
}
