<?php

declare(strict_types=1);

namespace Modules\Core\Http\Controllers\Api\Status;

use Modules\Core\Repositories\Status\StatusRepositoryInterface;
use App\Traits\ResponseJson;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Modules\Core\Http\Requests\Status\StoreStatusRequest;
use Modules\Core\Http\Requests\Status\UpdateStatusRequest;
use Modules\Core\Transformers\Status\StatusResource;

class StatusController extends Controller
{
    use ResponseJson;

    public function __construct(
        protected StatusRepositoryInterface $statusRepo
    ) {
        //
    }

    public function index(): JsonResponse
    {
        $data = $this->statusRepo->all();
        return $this->respondWithData(StatusResource::collection($data), 'Status list retrieved successfully');
    }

    public function show(int|string $id): JsonResponse
    {
        $data = $this->statusRepo->find($id);
        if (!$data) {
            return $this->respondError('Status not found', 404);
        }
        return $this->respondWithData(StatusResource::make($data), 'Status retrieved successfully');
    }

    public function store(StoreStatusRequest $request): JsonResponse
    {
        $data = $this->statusRepo->create($request->validated());
        return $this->respondWithData(StatusResource::make($data), 'Status created successfully', 201);
    }

    public function update(UpdateStatusRequest $request, int|string $id): JsonResponse
    {
        $data = $this->statusRepo->update($id, $request->validated());
        return $this->respondWithData(StatusResource::make($data), 'Status updated successfully');
    }

    public function destroy(int|string $id): JsonResponse
    {
        $this->statusRepo->delete($id);
        return $this->respondSuccess('Status deleted successfully');
    }


}
