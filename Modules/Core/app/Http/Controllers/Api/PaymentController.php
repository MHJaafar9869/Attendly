<?php

declare(strict_types=1);

namespace Modules\Core\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ResponseJson;
use Illuminate\Http\JsonResponse;
use Modules\Core\Http\Requests\Payment\StorePaymentRequest;
use Modules\Core\Http\Requests\Payment\UpdatePaymentRequest;
use Modules\Core\Repositories\Payment\PaymentRepositoryInterface;
use Modules\Core\Transformers\Payment\PaymentResource;

class PaymentController extends Controller
{
    use ResponseJson;

    public function __construct(
        protected PaymentRepositoryInterface $paymentRepo
    ) {
        //
    }

    public function index(): JsonResponse
    {
        $data = $this->paymentRepo->all();

        return $this->respondWithData(PaymentResource::collection($data), 'Payment list retrieved successfully');
    }

    public function show(int|string $id): JsonResponse
    {
        $data = $this->paymentRepo->find($id);
        if (! $data) {
            return $this->respondError('Payment not found', 404);
        }

        return $this->respondWithData(PaymentResource::make($data), 'Payment retrieved successfully');
    }

    public function store(StorePaymentRequest $request): JsonResponse
    {
        $data = $this->paymentRepo->create($request->validated());

        return $this->respondWithData(PaymentResource::make($data), 'Payment created successfully', 201);
    }

    public function update(UpdatePaymentRequest $request, int|string $id): JsonResponse
    {
        $data = $this->paymentRepo->update($id, $request->validated());

        return $this->respondWithData(PaymentResource::make($data), 'Payment updated successfully');
    }

    public function destroy(int|string $id): JsonResponse
    {
        $this->paymentRepo->delete($id);

        return $this->respondSuccess('Payment deleted successfully');
    }
}
