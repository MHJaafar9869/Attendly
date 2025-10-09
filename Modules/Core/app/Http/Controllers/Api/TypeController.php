<?php

declare(strict_types=1);

namespace Modules\Core\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ResponseJson;
use Illuminate\Http\JsonResponse;
use Modules\Core\Http\Requests\Type\StoreTypeRequest;
use Modules\Core\Http\Requests\Type\UpdateTypeRequest;
use Modules\Core\Repositories\Type\TypeRepositoryInterface;
use Modules\Core\Transformers\Type\TypeResource;

class TypeController extends Controller
{
    use ResponseJson;

    public function __construct(
        protected TypeRepositoryInterface $typeRepo
    ) {
        //
    }

    public function index(): JsonResponse
    {
        $data = $this->typeRepo->all();

        return $this->respondWithData(TypeResource::collection($data), 'Type list retrieved successfully');
    }

    public function show(int | string $id): JsonResponse
    {
        $data = $this->typeRepo->find($id);
        if (! $data) {
            return $this->respondError('Type not found', 404);
        }

        return $this->respondWithData(TypeResource::make($data), 'Type retrieved successfully');
    }

    public function store(StoreTypeRequest $request): JsonResponse
    {
        $data = $this->typeRepo->create($request->validated());

        return $this->respondWithData(TypeResource::make($data), 'Type created successfully', 201);
    }

    public function update(UpdateTypeRequest $request, int | string $id): JsonResponse
    {
        $data = $this->typeRepo->update($id, $request->validated());

        return $this->respondWithData(TypeResource::make($data), 'Type updated successfully');
    }

    public function destroy(int | string $id): JsonResponse
    {
        $this->typeRepo->delete($id);

        return $this->respondSuccess('Type deleted successfully');
    }
}
