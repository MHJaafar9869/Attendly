<?php

declare(strict_types=1);

namespace Modules\Core\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ResponseJson;
use Illuminate\Http\JsonResponse;
use Modules\Core\Http\Requests\Setting\StoreSettingRequest;
use Modules\Core\Http\Requests\Setting\UpdateSettingRequest;
use Modules\Core\Repositories\Setting\SettingRepositoryInterface;
use Modules\Core\Transformers\Setting\SettingResource;

class SettingController extends Controller
{
    use ResponseJson;

    public function __construct(
        protected SettingRepositoryInterface $settingRepo
    ) {
        //
    }

    public function index(): JsonResponse
    {
        $data = $this->settingRepo->all();

        return $this->respondWithData(SettingResource::collection($data), 'Setting list retrieved successfully');
    }

    public function show(int|string $id): JsonResponse
    {
        $data = $this->settingRepo->find($id);
        if (! $data) {
            return $this->respondError('Setting not found', 404);
        }

        return $this->respondWithData(SettingResource::make($data), 'Setting retrieved successfully');
    }

    public function store(StoreSettingRequest $request): JsonResponse
    {
        $data = $this->settingRepo->create($request->validated());

        return $this->respondWithData(SettingResource::make($data), 'Setting created successfully', 201);
    }

    public function update(UpdateSettingRequest $request, int|string $id): JsonResponse
    {
        $data = $this->settingRepo->update($id, $request->validated());

        return $this->respondWithData(SettingResource::make($data), 'Setting updated successfully');
    }

    public function destroy(int|string $id): JsonResponse
    {
        $this->settingRepo->delete($id);

        return $this->respondSuccess('Setting deleted successfully');
    }
}
