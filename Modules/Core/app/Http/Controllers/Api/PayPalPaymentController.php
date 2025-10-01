<?php

declare(strict_types=1);

namespace Modules\Core\Http\Controllers\Api;

use Modules\Core\Repositories\PayPalPayment\PayPalPaymentRepositoryInterface;
use App\Traits\ResponseJson;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Modules\Core\Http\Requests\PayPalPayment\StorePayPalPaymentRequest;
use Modules\Core\Http\Requests\PayPalPayment\UpdatePayPalPaymentRequest;
use Modules\Core\Transformers\PayPalPayment\PayPalPaymentResource;

class PayPalPaymentController extends Controller
{
    use ResponseJson;

    public function __construct(
        protected PayPalPaymentRepositoryInterface $paypalpaymentRepo
    ) {
        //
    }

    public function index(): JsonResponse
    {
        $data = $this->paypalpaymentRepo->all();
        return $this->respondWithData(PayPalPaymentResource::collection($data), 'PayPalPayment list retrieved successfully');
    }

    public function show(int|string $id): JsonResponse
    {
        $data = $this->paypalpaymentRepo->find($id);
        if (!$data) {
            return $this->respondError('PayPalPayment not found', 404);
        }
        return $this->respondWithData(PayPalPaymentResource::make($data), 'PayPalPayment retrieved successfully');
    }

    public function store(StorePayPalPaymentRequest $request): JsonResponse
    {
        $data = $this->paypalpaymentRepo->create($request->validated());
        return $this->respondWithData(PayPalPaymentResource::make($data), 'PayPalPayment created successfully', 201);
    }

    public function update(UpdatePayPalPaymentRequest $request, int|string $id): JsonResponse
    {
        $data = $this->paypalpaymentRepo->update($id, $request->validated());
        return $this->respondWithData(PayPalPaymentResource::make($data), 'PayPalPayment updated successfully');
    }

    public function destroy(int|string $id): JsonResponse
    {
        $this->paypalpaymentRepo->delete($id);
        return $this->respondSuccess('PayPalPayment deleted successfully');
    }


}
