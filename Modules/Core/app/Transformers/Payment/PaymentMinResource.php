<?php

namespace Modules\Core\Transformers\Payment;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentMinResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request)
    {
        $resource = $this->resource;

        return [
            'id' => $resource->id,
            'owner' => $resource->owner?->name,
            'user' => $resource->user?->name,
            'stripePaymentIntent' => $resource->stripePaymentIntent?->name,
            'amount' => $resource->amount,
            'currency' => $resource->currency,
            'product_data' => $this->getTranslations('product_data'),
            'status' => $resource->status,
            'created_at' => $resource->created_at,
            'updated_at' => $resource->updated_at,
        ];
    }
}
