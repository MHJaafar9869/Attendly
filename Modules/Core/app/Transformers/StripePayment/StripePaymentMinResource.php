<?php

namespace Modules\Core\Transformers\StripePayment;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StripePaymentMinResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request)
    {
        $resource = $this->resource;

        return [
            'id' => $resource->id,
            'buyer' => $resource->buyer?->name,
            'payable' => $resource->payable?->name,
            'payable_type' => $resource->payable_type,
            'stripePaymentIntent' => $resource->stripePaymentIntent?->name,
            'amount_cents' => $resource->amount_cents,
            'currency' => $resource->currency,
            'product_data' => $this->getTranslations('product_data'),
            'status' => $resource->status?->name,
            'created_at' => $resource->created_at,
            'updated_at' => $resource->updated_at,
        ];
    }
}
